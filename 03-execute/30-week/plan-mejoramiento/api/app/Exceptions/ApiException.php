
<?php

namespace App\Exceptions;

use Exception;

final class ApiException extends Exception
{
    private string $errorCode;

    private array $details;

    public function __construct(
        string $errorCode,
        string $message,
        int $statusCode = 422,
        array $details = []
    ) {
        parent::__construct($message, $statusCode);

        $this->errorCode = $errorCode;
        $this->details = $details;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getStatusCode(): int
    {
        return $this->getCode();
    }

    public function getDetails(): array
    {
        return $this->details;
    }

    public function toArray(): array
    {
        return [
            'code' => $this->errorCode,
            'message' => $this->getMessage(),
            'details' => $this->details,
        ];
    }
}
