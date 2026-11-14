
<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Support\JsonData;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class FamilyController extends BaseController
{
    // GET /api/v1/families
    public function index(Request $request): JsonResponse
    {
        $data = $request->validate([
            'q' => ['sometimes', 'string', 'max:200'],
        ]);

        $query = DB::table('families');

        if (!empty($data['q'])) {
            $term = '%' . trim($data['q']) . '%';

            $query->where(function ($search) use ($term) {
                $search->where(
                    'full_name',
                    'ilike',
                    $term
                )->orWhere(
                    'document_number',
                    'ilike',
                    $term
                );
            });
        }

        $families = JsonData::paged(
            $query->orderBy('full_name')->orderBy('id'),
            $request,
            fn ($row) => $row
        );

        return response()->json($families);
    }

    // GET /api/v1/families/{id}
    public function show(string $id): JsonResponse
    {
        $family = DB::table('families')
            ->where('id', $id)
            ->first();

        if (!$family) {
            throw new ApiException(
                'NOT_FOUND',
                'Familia no encontrada.',
                404
            );
        }

        // Count loan items that have not been returned.
        $activeLoans = DB::table('loan_items')
            ->join(
                'loans',
                'loans.id',
                '=',
                'loan_items.loan_id'
            )
            ->where('loans.family_id', $id)
            ->whereNull('loan_items.returned_at')
            ->count();

        return response()->json([
            'id' => $family->id,
            'full_name' => $family->full_name,
            'document_number' => $family->document_number,
            'active_loans' => $activeLoans,
        ]);
    }

    // POST /api/v1/families
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'full_name' => [
                'required',
                'string',
                'max:200'
            ],
            'document_number' => [
                'required',
                'string',
                'max:30'
            ],
        ]);

        $fullName = trim($data['full_name']);
        $document = trim($data['document_number']);

        if ($fullName === '' || $document === '') {
            throw new ApiException(
                'VALIDATION_ERROR',
                'El nombre y el documento son obligatorios.',
                422
            );
        }

        // Prevent duplicate document numbers.
        $exists = DB::table('families')
            ->where('document_number', $document)
            ->exists();

        if ($exists) {
            throw new ApiException(
                'DOCUMENT_TAKEN',
                'El documento ya está registrado.',
                409
            );
        }

        $id = (string) Str::uuid();

        try {
            DB::table('families')->insert([
                'id' => $id,
                'full_name' => $fullName,
                'document_number' => $document,
            ]);
        } catch (QueryException $exception) {
            // Handle duplicates registered concurrently.
            if ($exception->getCode() === '23505') {
                throw new ApiException(
                    'DOCUMENT_TAKEN',
                    'El documento ya está registrado.',
                    409
                );
            }

            throw $exception;
        }

        return response()->json([
            'id' => $id,
            'full_name' => $fullName,
            'document_number' => $document,
        ], 201);
    }
}
