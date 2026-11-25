
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
        // Only administrators can access reports.
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

        // Retrieve the most recent historical name
        // from the immutable loan item snapshots.
        $latestName = DB::table('loan_items as latest')
            ->join(
                'loans as latest_loan',
                'latest_loan.id',
                '=',
                'latest.loan_id'
            )
            ->select('latest.toy_name')
            ->whereColumn(
                'latest.toy_id',
                'loan_items.toy_id'
            )
            ->orderByDesc('latest_loan.loaned_at')
            ->orderByDesc('latest.id')
            ->limit(1);

        // Count historical loans by toy ID.
        // Soft-deleted toys remain represented.
        $report = DB::table('loan_items')
            ->leftJoin(
                'toys',
                'toys.id',
                '=',
                'loan_items.toy_id'
            )
            ->select('loan_items.toy_id')
            ->selectSub($latestName, 'name')
            ->selectRaw(
                'MAX(loan_items.reference) AS reference'
            )
            ->selectRaw(
                'MAX(toys.brand) AS brand'
            )
            ->selectRaw(
                'COUNT(loan_items.id) AS total_loans'
            )
            ->groupBy('loan_items.toy_id')
            ->orderByDesc('total_loans')
            ->orderBy('name')
            ->limit($limit)
            ->get();

        return response()->json([
            'data' => $report->map(function ($item) {
                return [
                    'toy_id' => $item->toy_id,
                    'name' => $item->name,
                    'brand' => $item->brand,
                    'reference' => $item->reference,
                    'total_loans' => (int) $item->total_loans,
                ];
            })->values(),
        ]);
    }
}
