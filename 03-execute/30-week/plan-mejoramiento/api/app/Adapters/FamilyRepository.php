<?php
namespace App\Adapters;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use App\Ports\FamilyPort;
use App\Exceptions\ApiException;
use Illuminate\Database\QueryException;

final class FamilyRepository implements FamilyPort
{
    public function search(string $term): Builder
    {
        $query = DB::table('families');
        if ($term !== '') {
            $pattern = '%' . $term . '%';
            $query->where(fn ($search) => $search->where('full_name', 'ilike', $pattern)->orWhere('document_number', 'ilike', $pattern));
        }
        return $query->orderBy('full_name')->orderBy('id');
    }

    public function find(string $id): ?object
    {
        return DB::table('families')->where('id', $id)->first();
    }

    public function pendingItems(string $id)
    {
        return DB::table('loan_items')->join('loans', 'loans.id', '=', 'loan_items.loan_id')
            ->where('loans.family_id', $id)->whereNull('loan_items.returned_at')
            ->select('loan_items.id', 'loan_items.loan_id', 'loan_items.toy_id', 'loan_items.toy_name', 'loan_items.due_at')
            ->orderBy('loan_items.due_at')->get();
    }

    public function documentExists(string $document): bool
    {
        return DB::table('families')->where('document_number', $document)->exists();
    }

    public function insert(array $values): void
    {
        try {
            DB::table('families')->insert($values);
        } catch (QueryException $exception) {
            if ($exception->getCode() === '23505') throw new ApiException('DOCUMENT_TAKEN', 'El documento ya está registrado.', 409);
            throw $exception;
        }
    }
}
