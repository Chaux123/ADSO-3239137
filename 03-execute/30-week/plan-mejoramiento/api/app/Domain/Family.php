<?php
namespace App\Domain;

use InvalidArgumentException;

final readonly class Family
{
    public string $fullName;
    public string $documentNumber;

    public function __construct(string $fullName, string $documentNumber)
    {
        $fullName = trim($fullName);
        $documentNumber = trim($documentNumber);
        if ($fullName === '' || $documentNumber === '' || mb_strlen($fullName) > 200 || mb_strlen($documentNumber) > 30) {
            throw new InvalidArgumentException('El nombre y el documento de la familia no son válidos.');
        }
        $this->fullName = $fullName;
        $this->documentNumber = $documentNumber;
    }
}
