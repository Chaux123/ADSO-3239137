<?php
namespace App\Ports;
interface PromoterPort {
    public function findByUsername(string $username): ?object;
    public function findById(string $id): ?object;
    public function usernameExists(string $username): bool;
    public function insert(array $values): void;
}
