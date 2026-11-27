<?php

namespace App\Support;

use Carbon\CarbonImmutable;
use App\Domain\LoanRules;
use Illuminate\Support\Collection;

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
            'category' => ['id' => (string) $toy->category_id, 'name' => $toy->category_name ?? null],
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
    public static function loan(object $loan, Collection $items): array
    {
        $status = LoanRules::status(
            $items->map(fn ($item) => ['due_at' => $item->due_at, 'returned_at' => $item->returned_at])->all(),
            CarbonImmutable::now('UTC')->toDateTimeImmutable()
        );
        $formattedItems = $items->map(
            function ($item): array {
                return [
                    'id' => (string) $item->id,
                    'toy_id' => (string) $item->toy_id,
                    'toy_name' => $item->toy_name,
                    'reference' => $item->reference,
                    'category_name' => $item->category_name,
                    'due_at' => CarbonImmutable::parse($item->due_at, 'UTC')->toISOString(),
                    'returned_at' => $item->returned_at === null ? null : CarbonImmutable::parse($item->returned_at, 'UTC')->toISOString(),
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
            'loaned_at' => CarbonImmutable::parse($loan->loaned_at, 'UTC')->toISOString(),
            'status' => $status,
            'items' => $formattedItems,
        ];
    }
}
