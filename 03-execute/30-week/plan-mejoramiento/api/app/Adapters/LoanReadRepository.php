<?php
namespace App\Adapters;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

final class LoanReadRepository
{
    public function search(array $filters): Builder
    {
        $query = DB::table('loans')->orderByDesc('loaned_at')->orderBy('id');
        if (!empty($filters['family_id'])) $query->where('loans.family_id', $filters['family_id']);
        if (!empty($filters['from'])) $query->where('loaned_at', '>=', $filters['from'] . ' 00:00:00');
        if (!empty($filters['to'])) $query->where('loaned_at', '<=', $filters['to'] . ' 23:59:59.999999');
        if (!empty($filters['status'])) {
            $now = now('UTC');
            $pending = fn ($q) => $q->selectRaw('1')->from('loan_items')->whereColumn('loan_items.loan_id', 'loans.id')->whereNull('loan_items.returned_at');
            $overdue = fn ($q) => $q->selectRaw('1')->from('loan_items')->whereColumn('loan_items.loan_id', 'loans.id')->whereNull('loan_items.returned_at')->where('loan_items.due_at', '<', $now);
            $status = $filters['status'];
            if (in_array($status, ['closed', 'returned'], true)) $query->whereNotExists($pending);
            elseif ($status === 'overdue') $query->whereExists($overdue);
            elseif (in_array($status, ['open', 'active'], true)) $query->whereExists($pending)->whereNotExists($overdue);
        }
        return $query;
    }

    public function find(string $id): ?object
    {
        return DB::table('loans')->where('id', $id)->first();
    }

    public function items(string $loanId): \Illuminate\Support\Collection
    {
        return DB::table('loan_items')->where('loan_id', $loanId)->orderBy('id')->get();
    }

    public function overdue($now)
    {
        return DB::table('loan_items')->join('loans', 'loans.id', '=', 'loan_items.loan_id')
            ->whereNull('loan_items.returned_at')->where('loan_items.due_at', '<', $now)
            ->orderBy('loan_items.due_at')
            ->select('loan_items.id as loan_item_id', 'loan_items.loan_id', 'loans.family_id', 'loan_items.toy_name', 'loan_items.due_at')->get();
    }
}
