
<?php

namespace App\Http\Controllers;

use App\Application\LoanUseCase;
use App\Application\ReturnUseCase;
use App\Exceptions\ApiException;
use App\Support\JsonData;
use App\Support\Presenters;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class LoanController extends BaseController
{
    // GET /api/v1/loans
    public function index(Request $request): JsonResponse
    {
        $data = $request->validate([
            'family_id' => ['sometimes', 'uuid'],
            'status' => [
                'sometimes',
                'in:active,overdue,returned'
            ],
        ]);

        $query = DB::table('loans')
            ->orderByDesc('loaned_at')
            ->orderBy('id');

        if (!empty($data['family_id'])) {
            $query->where(
                'loans.family_id',
                $data['family_id']
            );
        }

        // Filter loan status before pagination.
        if (!empty($data['status'])) {
            $now = now('UTC');
            $status = $data['status'];

            $hasPendingItems = function ($query) {
                $query->selectRaw('1')
                    ->from('loan_items')
                    ->whereColumn(
                        'loan_items.loan_id',
                        'loans.id'
                    )
                    ->whereNull('loan_items.returned_at');
            };

            $hasOverdueItems = function ($query) use ($now) {
                $query->selectRaw('1')
                    ->from('loan_items')
                    ->whereColumn(
                        'loan_items.loan_id',
                        'loans.id'
                    )
                    ->whereNull('loan_items.returned_at')
                    ->where('loan_items.due_at', '<', $now);
            };

            if ($status === 'returned') {
                // Every toy in the loan was returned.
                $query->whereNotExists($hasPendingItems);

            } elseif ($status === 'overdue') {
                // At least one unreturned toy is overdue.
                $query->whereExists($hasOverdueItems);

            } elseif ($status === 'active') {
                // There are pending toys, but none overdue.
                $query->whereExists($hasPendingItems)
                    ->whereNotExists($hasOverdueItems);
            }
        }

        $result = JsonData::paged(
            $query,
            $request,
            fn ($loan) => Presenters::loan($loan)
        );

        return response()->json($result);
    }

    // GET /api/v1/loans/{id}
    public function show(string $id): JsonResponse
    {
        $loan = DB::table('loans')
            ->where('id', $id)
            ->first();

        if (!$loan) {
            throw new ApiException(
                'NOT_FOUND',
                'Préstamo no encontrado.',
                404
            );
        }

        return response()->json(
            Presenters::loan($loan)
        );
    }

    // POST /api/v1/loans
    public function store(
        Request $request,
        LoanUseCase $useCase
    ): JsonResponse {
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
