<?php

namespace Tests\Feature;

use App\Adapters\DatabaseLoanAdapter;
use App\Exceptions\ApiException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class LoanConcurrencyTest extends TestCase
{
    use RefreshDatabase;

    private string $familyOneId =
        '00000000-0000-4000-8000-000000000001';

    private string $familyTwoId =
        '00000000-0000-4000-8000-000000000002';

    private string $toyId =
        '00000000-0000-4000-8000-000000000003';

    private string $categoryId =
        '00000000-0000-4000-8000-000000000004';

    private string $promoterId =
        '00000000-0000-4000-8000-000000000005';

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('categories')->insert([
            'id' => $this->categoryId,
            'name' => 'Juguetes educativos',
        ]);

        DB::table('families')->insert([
            [
                'id' => $this->familyOneId,
                'full_name' => 'Familia Uno',
                'document_number' => '100000001',
            ],
            [
                'id' => $this->familyTwoId,
                'full_name' => 'Familia Dos',
                'document_number' => '100000002',
            ],
        ]);

        DB::table('promoters')->insert([
            'id' => $this->promoterId,
            'username' => 'promotor_pruebas',
            'password_hash' => Hash::make('secure-password-123'),
            'role' => 'promoter',
        ]);

        DB::table('toys')->insert([
            'id' => $this->toyId,
            'name' => 'Rompecabezas',
            'brand' => 'Marca Educativa',
            'reference' => 'TEST-001',
            'category_id' => $this->categoryId,
            'total_units' => 1,
            'available_units' => 1,
            'cover_key' => null,
            'deleted_at' => null,
        ]);
    }

    public function test_first_family_can_borrow_last_available_toy(): void
    {
        $adapter = new DatabaseLoanAdapter();

        $result = $adapter->create(
            $this->familyOneId,
            $this->promoterId,
            [$this->toyId]
        );

        $this->assertIsArray($result);

        $this->assertSame(
            0,
            DB::table('toys')
                ->where('id', $this->toyId)
                ->value('available_units')
        );

        $this->assertSame(
            1,
            DB::table('loan_items')
                ->where('toy_id', $this->toyId)
                ->count()
        );
    }

    public function test_second_family_cannot_borrow_unavailable_toy(): void
    {
        $adapter = new DatabaseLoanAdapter();

        // First family borrows the last available unit.
        $adapter->create(
            $this->familyOneId,
            $this->promoterId,
            [$this->toyId]
        );

        try {
            // Second family attempts to borrow the same toy.
            $adapter->create(
                $this->familyTwoId,
                $this->promoterId,
                [$this->toyId]
            );

            $this->fail(
                'The second loan should have been rejected.'
            );
        } catch (ApiException $exception) {
            $this->assertSame(
                0,
                DB::table('toys')
                    ->where('id', $this->toyId)
                    ->value('available_units')
            );

            $this->assertSame(
                1,
                DB::table('loan_items')
                    ->where('toy_id', $this->toyId)
                    ->count()
            );
        }
    }
}
