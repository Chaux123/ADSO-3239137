
<?php

namespace App\Ports;

interface LoanPort
{
    /**
     * Create a loan for a family.
     *
     * @param string $familyId
     * @param string $promoterId
     * @param array $toyIds
     * @return array
     */
    public function create(
        string $familyId,
        string $promoterId,
        array $toyIds
    ): array;

    /**
     * Register the return of a loan item.
     *
     * @param string $loanId
     * @param string $itemId
     * @return array
     */
    public function returnItem(
        string $loanId,
        string $itemId
    ): array;
}
