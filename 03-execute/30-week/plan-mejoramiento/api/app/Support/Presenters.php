
<?php

namespace App\Support;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

final class Presenters
{
    /**
     * Format toy data for API responses.
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
     * Format loan data and its historical toy snapshots.
     */
    public static function loan(object $loan): array
    {
        $items = DB::table('loan_items')
            ->where('loan_id', $loan->id)
            ->orderBy('id')
            ->get();

        $now = CarbonImmutable::now('UTC');

        $allReturned = $items->isNotEmpty()
            && $items->every(
                fn ($item) => $item->returned_at !== null
            );

        $hasOverdue = $items->contains(
            function ($item) use ($now): bool {
                return $item->returned_at === null
                    && CarbonImmutable::parse(
                        $item->due_at,
                        'UTC'
                    )->lessThan($now);
            }
        );

        if ($allReturned) {
            $status = 'returned';
        } elseif ($hasOverdue) {
            $status = 'overdue';
        } else {
            $status = 'active';
        }

        $formattedItems = $items->map(
            function ($item): array {
                return [
                    'id' => (string) $item->id,
                    'toy_id' => (string) $item->toy_id,
                    'due_at' => $item->due_at,
                    'returned_at' => $item->returned_at,
                    'snapshot' => [
                        'toy_name' => $item->toy_name,
                        'reference' => $item->reference,
                        'category_name' => $item->category_name,
                    ],
                ];
            }
        )->values()->all();

        return [
            'id' => (string) $loan->id,
            'family_id' => (string) $loan->family_id,
            'promoter_id' => (string) $loan->loaned_by_promoter_id,
            'loaned_at' => $loan->loaned_at,
            'status' => $status,
            'items' => $formattedItems,
        ];
    }
}
