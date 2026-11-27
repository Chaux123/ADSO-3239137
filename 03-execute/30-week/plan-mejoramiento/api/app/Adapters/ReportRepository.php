<?php
namespace App\Adapters;

use Illuminate\Support\Facades\DB;

final class ReportRepository
{
    public function mostLoaned(array $data)
    {
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
            ->whereBetween('latest_loan.loaned_at', [$data['from'] . ' 00:00:00', $data['to'] . ' 23:59:59.999999'])
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
            ->join('loans', 'loans.id', '=', 'loan_items.loan_id')
            ->whereBetween('loans.loaned_at', [$data['from'] . ' 00:00:00', $data['to'] . ' 23:59:59.999999'])
            ->leftJoin(
                'toys',
                'toys.id',
                '=',
                'loan_items.toy_id'
            )
            ->select('loan_items.toy_id')
            ->selectSub($latestName, 'name')
            ->selectSub((clone $latestName)->select('latest.category_name'), 'category_name')
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

        return $report;
    }
}