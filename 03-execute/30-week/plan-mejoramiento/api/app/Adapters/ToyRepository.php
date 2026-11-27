<?php
namespace App\Adapters;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

final class ToyRepository
{
    public function activeQuery(): Builder
    {
        return DB::table('toys')->join('categories', 'categories.id', '=', 'toys.category_id')
            ->whereNull('toys.deleted_at')->select('toys.*', 'categories.name as category_name');
    }

    public function categories()
    {
        return DB::table('categories')->orderBy('name')->get();
    }

    public function referenceExists(string $reference): bool
    {
        return DB::table('toys')->where('reference', $reference)->whereNull('deleted_at')->exists();
    }

    public function insert(array $values): void
    {
        DB::table('toys')->insert($values);
    }

    public function transaction(callable $operation): mixed
    {
        return DB::transaction($operation);
    }

    public function lockActive(string $id): ?object
    {
        return DB::table('toys')->where('id', $id)->whereNull('deleted_at')->lockForUpdate()->first();
    }

    public function update(string $id, array $values): void
    {
        DB::table('toys')->where('id', $id)->update($values);
    }

    public function hasOpenLoans(string $id): bool
    {
        return DB::table('loan_items')->where('toy_id', $id)->whereNull('returned_at')->exists();
    }

    public function coverKey(string $id): ?string
    {
        return DB::table('toys')->where('id', $id)->value('cover_key');
    }
}
