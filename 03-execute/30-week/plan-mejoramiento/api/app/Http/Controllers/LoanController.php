
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
            ->orderByDesc('created_at')
            ->orderBy('id');

        if (!empty($data['family_id'])) {
            $query->where(
                'family_id',
                $data['family_id']
            );
        }

        $result = JsonData::paged(
            $query,
            $request,
            function ($loan) {
                return Presenters::loan($loan);
            }
        );

        if (!empty($data['status'])) {
            $result['data'] = array_values(
                array_filter(
                    $result['data'],
                    fn ($loan) =>
                        $loan['status'] === $data['status']
                )
            );
        }

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

        $promoter = $request->attributes->get(
            'promoter'
        );

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
        $promoter = $request->attributes->get(
            'promoter'
        );

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
