
<?php

namespace App\Application;

use App\Ports\LoanPort;
use App\Exceptions\ApiException;

final class LoanUseCase
{
    public function __construct(
        private LoanPort $store
    ) {
    }

    public function execute(
        string $familyId,
        string $promoterId,
        array $toyIds
    ): array {
        if (count($toyIds) < 1 || count($toyIds) > 3) {
            throw new ApiException(
                'VALIDATION_ERROR',
                'El préstamo debe tener entre 1 y 3 juguetes.',
                422
            );
        }

        if (count($toyIds) !== count(array_unique($toyIds))) {
            throw new ApiException(
                'DUPLICATE_TOY_IN_LOAN',
                'Un mismo juguete no puede repetirse.',
                422
            );
        }

        return $this->store->create(
            $familyId,
            $promoterId,
            $toyIds
        );
    }
}
