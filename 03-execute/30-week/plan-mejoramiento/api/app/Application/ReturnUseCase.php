
<?php

namespace App\Application;

use App\Ports\LoanPort;

final class ReturnUseCase
{
    public function __construct(
        private LoanPort $store
    ) {
    }

    public function execute(
        string $loanId,
        string $itemId
    ): array {
        return $this->store->returnItem(
            $loanId,
            $itemId
        );
    }
}
