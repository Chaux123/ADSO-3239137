
<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class ReportController extends BaseController
{
    // GET /api/v1/reports/most-loaned
    public function mostLoaned(Request $request): JsonResponse
    {
        $this->admin($request);

        $data = $request->validate([
            'limit' => [
                'sometimes',
                'integer',
                'min:1',
                'max:100',
            ],
        ]);

        $limit = (int) ($data['limit'] ?? 10);

        $report = DB::table('loan_items')
            ->join(
                'toys',
                'toys.id',
                '=',
                'loan_items.toy_id'
            )
            ->select(
                'toys.id',
                'toys.name',
                'toys.brand',
                'toys.reference'
            )
            ->selectRaw(
                'COUNT(loan_items.id) AS total_loans'
            )
            ->groupBy(
                'toys.id',
                'toys.name',
                'toys.brand',
                'toys.reference'
            )
            ->orderByDesc('total_loans')
            ->orderBy('toys.name')
            ->limit($limit)
            ->get();

        return response()->json([
            'data' => $report->map(function ($item) {
                return [
                    'toy_id' => $item->id,
                    'name' => $item->name,
                    'brand' => $item->brand,
                    'reference' => $item->reference,
                    'total_loans' => (int) $item->total_loans,
                ];
            })->values(),
        ]);
    }
}
