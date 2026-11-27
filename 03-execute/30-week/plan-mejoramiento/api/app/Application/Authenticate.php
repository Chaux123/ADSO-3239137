<?php
namespace App\Application;
use App\Exceptions\ApiException;
use App\Ports\PromoterPort;
use App\Support\JwtService;
use Illuminate\Support\Facades\Hash;
final class Authenticate {
    public function __construct(private PromoterPort $promoters, private JwtService $jwt) {}
    public function execute(string $username, string $password): array {
        $promoter = $this->promoters->findByUsername(strtolower(trim($username)));
        if (!$promoter || !Hash::check($password, $promoter->password_hash)) {
            throw new ApiException('INVALID_CREDENTIALS', 'Las credenciales proporcionadas son incorrectas.', 401);
        }
        return ['access_token' => $this->jwt->issue((string) $promoter->id, (string) $promoter->role),
            'token_type' => 'Bearer', 'expires_in' => 28800, 'role' => $promoter->role];
    }
}
