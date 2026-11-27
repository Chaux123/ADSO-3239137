<?php

namespace App\Application;

use App\Ports\FamilyPort;
use App\Domain\Family;
use App\Exceptions\ApiException;
use Illuminate\Support\Str;

final class RegisterFamily
{
    public function __construct(private FamilyPort $families) {}

    public function execute(string $name, string $document): array
    {
        try {
            $family = new Family($name, $document);
        } catch (\InvalidArgumentException $exception) {
            throw new ApiException('VALIDATION_ERROR', $exception->getMessage(), 422);
        }
        if ($this->families->documentExists($family->documentNumber)) {
            throw new ApiException('DOCUMENT_TAKEN', 'El documento ya está registrado.', 409);
        }
        $values = ['id' => (string) Str::uuid(), 'full_name' => $family->fullName, 'document_number' => $family->documentNumber];
        $this->families->insert($values);
        return $values;
    }
}
