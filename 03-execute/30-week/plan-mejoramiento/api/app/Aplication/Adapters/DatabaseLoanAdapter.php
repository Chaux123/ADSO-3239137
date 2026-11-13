
<?php

namespace App\Adapters;

use App\Ports\LoanPort;
use App\Domain\LoanRules;
use App\Exceptions\ApiException;
use App\Support\Presenters;
use App\Support\JsonData;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class DatabaseLoanAdapter implements LoanPort
{
    public function create(
        string $familyId,
        string $promoterId,
        array $toyIds
    ): array {
        return DB::transaction(function () use (
            $familyId,
            $promoterId,
            $toyIds
        ) {
            // Lock the family to prevent concurrent limit violations.
            $family = DB::table('families')
                ->where('id', $familyId)
                ->lockForUpdate()
                ->first();

            if (!$family) {
                throw new ApiException(
                    'NOT_FOUND',
                    'Familia no encontrada.',
                    404
                );
            }

            $now = CarbonImmutable::now('UTC');

            // Count all pending toys for this family.
            $openItems = DB::table('loan_items')
                ->join(
                    'loans',
                    'loans.id',
                    '=',
                    'loan_items.loan_id'
                )
                ->where('loans.family_id', $familyId)
                ->whereNull('loan_items.returned_at');

            $pendingCount = (clone $openItems)->count();

            $hasOverdue = (clone $openItems)
                ->where('loan_items.due_at', '<', $now)
                ->exists();

            if ($hasOverdue) {
                throw new ApiException(
                    'FAMILY_HAS_OVERDUE',
                    'La familia tiene juguetes vencidos sin devolver.',
                    409
                );
            }

            if ($pendingCount + count($toyIds) > 3) {
                throw new ApiException(
                    'FAMILY_LIMIT_EXCEEDED',
                    'La familia supera el límite de 3 juguetes.',
                    422,
                    [
                        [
                            'field' => 'items',
                            'issue' => 'Tiene '
                                . $pendingCount
                                . ' juguetes pendientes.'
                        ]
                    ]
                );
            }

            // Validate toys without allowing repeated IDs.
            if (
                count($toyIds) < 1 ||
                count($toyIds) > 3 ||
                count(array_unique($toyIds)) !== count($toyIds)
            ) {
                throw new ApiException(
                    'VALIDATION_ERROR',
                    'La selección de juguetes no es válida.',
                    422
                );
            }

            // Lock toys in a deterministic order.
            $sortedToyIds = $toyIds;
            sort($sortedToyIds, SORT_STRING);

            $toys = [];

            foreach ($sortedToyIds as $toyId) {
                $toy = DB::table('toys')
                    ->where('id', $toyId)
                    ->whereNull('deleted_at')
                    ->lockForUpdate()
                    ->first();

                if (!$toy) {
                    throw new ApiException(
                        'NOT_FOUND',
                        'Uno de los juguetes no existe.',
                        404
                    );
                }

                if ($toy->available_units < 1) {
                    throw new ApiException(
                        'TOY_NOT_AVAILABLE',
                        'No hay unidades disponibles.',
                        409
                    );
                }

                $toys[$toyId] = $toy;
            }

            $loanId = (string) Str::uuid();
            $dueAt = $now->addDays(9);

            // Create the loan.
            DB::table('loans')->insert([
                'id' => $loanId,
                'family_id' => $familyId,
                'loaned_by_promoter_id' => $promoterId,
                'loaned_at' => $now
            ]);

            // Create immutable loan snapshots.
            foreach ($toyIds as $toyId) {
                $toy = $toys[$toyId];

                $category = DB::table('categories')
                    ->where('id', $toy->category_id)
                    ->first();

                if (!$category) {
                    throw new ApiException(
                        'NOT_FOUND',
                        'Categoría no encontrada.',
                        404
                    );
                }

                DB::table('loan_items')->insert([
                    'id' => (string) Str::uuid(),
                    'loan_id' => $loanId,
                    'toy_id' => $toyId,
                    'toy_name' => $toy->name,
                    'reference' => $toy->reference,
                    'category_name' => $category->name,
                    'due_at' => $dueAt,
                    'returned_at' => null
                ]);

                // Decrease available inventory.
                DB::table('toys')
                    ->where('id', $toyId)
                    ->update([
                        'available_units' => $toy->available_units - 1
                    ]);
            }

            $loan = DB::table('loans')
                ->where('id', $loanId)
                ->first();

            return Presenters::loan($loan);

        }, 3);
    }

    public function returnItem(
        string $loanId,
        string $itemId
    ): array {
        return DB::transaction(function () use (
            $loanId,
            $itemId
        ) {
            $loan = DB::table('loans')
                ->where('id', $loanId)
                ->first();

            if (!$loan) {
                throw new ApiException(
                    'NOT_FOUND',
                    'Préstamo no encontrado.',
                    404
                );
            }

            $item = DB::table('loan_items')
                ->where('id', $itemId)
                ->where('loan_id', $loanId)
                ->first();

            if (!$item) {
                throw new ApiException(
                    'NOT_FOUND',
                    'Línea de préstamo no encontrada.',
                    404
                );
            }

            // Lock the toy before modifying its inventory.
            $toy = DB::table('toys')
                ->where('id', $item->toy_id)
                ->lockForUpdate()
                ->first();

            if (!$toy) {
                throw new ApiException(
                    'NOT_FOUND',
                    'Juguete no encontrado.',
                    404
                );
            }

            // Lock the loan item to prevent duplicate returns.
            $item = DB::table('loan_items')
                ->where('id', $itemId)
                ->where('loan_id', $loanId)
                ->lockForUpdate()
                ->first();

            if ($item->returned_at !== null) {
                throw new ApiException(
                    'ITEM_ALREADY_RETURNED',
                    'Este juguete ya fue devuelto.',
                    409
                );
            }

            if ($toy->available_units >= $toy->total_units) {
                throw new ApiException(
                    'VALIDATION_ERROR',
                    'Las unidades disponibles no pueden superar el total.',
                    422
                );
            }

            $now = CarbonImmutable::now('UTC');

            // Register the return.
            DB::table('loan_items')
                ->where('id', $itemId)
                ->update([
                    'returned_at' => $now
                ]);

            // Restore one available unit.
            DB::table('toys')
                ->where('id', $toy->id)
                ->update([
                    'available_units' => $toy->available_units + 1
                ]);

            return [
                'id' => $item->id,
                'loan_id' => $loanId,
                'toy_id' => $item->toy_id,
                'returned_at' => JsonData::utc((string) $now)
            ];

        }, 3);
    }
}
