
<?php

namespace App\Support;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;

final class JsonData
{
    /**
     * Paginate a database query and transform its results.
     */
    public static function paged(
        Builder $query,
        Request $request,
        callable $presenter
    ): array {
        $data = $request->validate([
            'page' => [
                'sometimes',
                'integer',
                'min:1'
            ],
            'per_page' => [
                'sometimes',
                'integer',
                'min:1',
                'max:100'
            ],
        ]);

        $page = (int) ($data['page'] ?? 1);
        $perPage = (int) ($data['per_page'] ?? 10);

        $total = (clone $query)->count();

        $results = $query
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        $items = $results
            ->map(fn ($item) => $presenter($item))
            ->values()
            ->all();

        return [
            'data' => $items,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => (int) ceil(
                    $total / $perPage
                ),
            ],
        ];
    }
}
