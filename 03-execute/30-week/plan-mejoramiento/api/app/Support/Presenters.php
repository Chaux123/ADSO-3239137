
<?php

namespace App\Support;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

final class Presenters
{
    /**
     * Format a toy for API responses.
     */
    public static function toy(object $toy): array
    {
        return [
            'id' => (string) $toy->id,
            'name' => (string) $toy->name,
            'brand' => (string) $toy->brand,
            'reference' => (string) $toy->reference,
            'category_id' => (string) $toy->category_id,
            'category_name' => $toy->category_name ?? null,
            'total_units' => (int) $toy->total_units,
            'available_units' => (int) $toy->available_units,
            'cover_key' => $toy->cover_key ?? null,
        ];
    }

    /**
     * Format a loan with its historical items.
     */
    public static function loan(object $loan): array
    {
        $items = DB::table('loan_items')
            ->where('loan_id', $loan->id)
            ->orderBy('id')
            ->get();

        $formattedItems = $items->map(
            function ($item): array {
                $values = (array) $item;

                // Preserve the snapshot values stored
                // when the loan was created.
                $snapshot = [];

                foreach ($values as $key => $value) {
                    if (str_starts_with($key, 'snapshot_')) {
                        $snapshot[$key] = $value;
                    }
                }

                return [
                    'id' => (string) $item->id,
                    'toy_id' => (string) $item->toy_id,
                    'returned_at' => $item->returned_at,
                    'snapshot' => $snapshot,
                ];
            }
        )->values()->all();

        $allReturned = count($formattedItems) > 0;

        foreach ($formattedItems as $item) {
            if ($item['returned_at'] === null) {
                $allReturned = false;
                break;
            }
        }

        $dueAt = CarbonImmutable::parse(
            $loan->due_at,
            'UTC'
        );

        if ($allReturned) {
            $status = 'returned';
        } elseif ($dueAt->lessThan(
            CarbonImmutable::now('UTC')
        )) {
            $status = 'overdue';
        } else {
            $status = 'active';
        }

        return [
            'id' => (string) $loan->id,
            'family_id' => (string) $loan->family_id,
            'promoter_id' => (string) $loan->promoter_id,
            'created_at' => $loan->created_at,
            'due_at' => $loan->due_at,
            'status' => $status,
            'items' => $formattedItems,
        ];
    }
}
