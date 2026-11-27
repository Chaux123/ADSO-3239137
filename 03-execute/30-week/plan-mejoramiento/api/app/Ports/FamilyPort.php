<?php
namespace App\Ports;

interface FamilyPort
{
    public function documentExists(string $document): bool;
    public function insert(array $values): void;
}
