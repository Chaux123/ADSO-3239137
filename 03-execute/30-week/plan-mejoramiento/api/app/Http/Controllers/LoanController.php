<?php

namespace App\Http\Controllers;

use App\Application\LoanUseCase;
use App\Adapters\LoanReadRepository;
use App\Application\ReturnUseCase;
use App\Exceptions\ApiException;
use App\Support\JsonData;
use App\Support\Presenters;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


final class LoanController extends BaseController
{
    public function __construct(private LoanReadRepository $loans) {}

    // GET /api/v1/loans
    public function index(Request $request): JsonResponse
    {
        $data = $request->validate([
            'family_id' => ['sometimes', 'uuid'],
            'from' => ['sometimes', 'date_format:Y-m-d'],
            'to' => ['sometimes', 'date_format:Y-m-d', 'after_or_equal:from'],
            'status' => [
                'sometimes',
                'in:open,overdue,closed,active,returned'
            ],
        ]);

        $query = $this->loans->search($data);

        $result = JsonData::paged(
            $query,
            $request,
            fn ($loan) => Presenters::loan($loan, $this->loans->items($loan->id))
        );

        return response()->json($result);
    }

    // GET /api/v1/loans/overdue
    public function overdue(): JsonResponse
    {
        $now = now('UTC');
        $items = $this->loans->overdue($now)
            ->map(function ($item) use ($now) {
                $due = \Carbon\CarbonImmutable::parse($item->due_at, 'UTC');
                return [
                    'loan_id' => $item->loan_id,
                    'loan_item_id' => $item->loan_item_id,
                    'family_id' => $item->family_id,
                    'toy_name' => $item->toy_name,
                    'due_at' => $due->toISOString(),
                    'days_late' => (int) floor($due->diffInDays($now)),
                ];
            });
        return response()->json($items);
    }
    // GET /api/v1/loans/{id}
    public function show(string $id): JsonResponse
    {
        $loan = $this->loans->find($id);

        if (!$loan) {
            throw new ApiException(
                'NOT_FOUND',
                'Préstamo no encontrado.',
                404
            );
        }

        return response()->json(
            Presenters::loan($loan, $this->loans->items($loan->id))
        );
    }

    // POST /api/v1/loans
    public function store(
        Request $request,
        LoanUseCase $useCase
    ): JsonResponse {
        if (!$request->has('toy_ids') && is_array($request->input('items'))) {
            $request->merge(['toy_ids' => array_map(fn ($item) => is_array($item) ? ($item['toy_id'] ?? null) : null, $request->input('items'))]);
        }
        $data = $request->validate([
            'family_id' => ['required', 'uuid'],
            'toy_ids' => [
                'required',
                'array',
                'min:1',
                'max:3'
            ],
            'toy_ids.*' => [
                'required',
                'uuid',
                'distinct'
            ],
        ]);

        $promoter = $request->attributes->get('promoter');

        if (!$promoter) {
            throw new ApiException(
                'UNAUTHORIZED',
                'Debe iniciar sesión para continuar.',
                401
            );
        }

        $loan = $useCase->execute(
            $data['family_id'],
            (string) $promoter->id,
            $data['toy_ids']
        );

        return response()->json($loan, 201);
    }

    // POST /api/v1/loans/{loanId}/items/{itemId}/return
    public function returnItem(
        Request $request,
        ReturnUseCase $useCase,
        string $loanId,
        string $itemId
    ): JsonResponse {
        $promoter = $request->attributes->get('promoter');

        if (!$promoter) {
            throw new ApiException(
                'UNAUTHORIZED',
                'Debe iniciar sesión para continuar.',
                401
            );
        }

        $loan = $useCase->execute(
            $loanId,
            $itemId
        );

        return response()->json($loan);
    }
}
