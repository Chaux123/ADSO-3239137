
<?php

namespace App\Support;

use RuntimeException;

final class JwtService
{
    private const TOKEN_DURATION = 28800;

    private function secret(): string
    {
        $secret = config('services.jwt.secret');

        if (!is_string($secret) || strlen($secret) < 32) {
            throw new RuntimeException(
                'JWT_SECRET debe tener al menos 32 caracteres.'
            );
        }

        return $secret;
    }

    private function encode(string $value): string
    {
        return rtrim(
            strtr(base64_encode($value), '+/', '-_'),
            '='
        );
    }

    private function decode(string $value): string
    {
        if (
            $value === '' ||
            !preg_match('/^[A-Za-z0-9_-]+$/D', $value)
        ) {
            throw new RuntimeException('Token inválido.');
        }

        $decoded = base64_decode(
            strtr($value, '-_', '+/'),
            true
        );

        if ($decoded === false ||
            $this->encode($decoded) !== $value) {
            throw new RuntimeException('Token inválido.');
        }

        return $decoded;
    }

    public function issue(
        string $promoterId,
        string $role
    ): string {
        if (
            $promoterId === '' ||
            !in_array($role, ['admin', 'promoter'], true)
        ) {
            throw new RuntimeException(
                'Datos de autenticación inválidos.'
            );
        }

        $now = time();

        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256',
        ];

        $payload = [
            'sub' => $promoterId,
            'role' => $role,
            'iat' => $now,
            'exp' => $now + self::TOKEN_DURATION,
        ];

        $headerPart = $this->encode(
            json_encode($header, JSON_THROW_ON_ERROR)
        );

        $payloadPart = $this->encode(
            json_encode($payload, JSON_THROW_ON_ERROR)
        );

        $message = $headerPart . '.' . $payloadPart;

        $signature = hash_hmac(
            'sha256',
            $message,
            $this->secret(),
            true
        );

        return $message . '.' . $this->encode($signature);
    }

    public function verify(string $token): array
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            throw new RuntimeException('Token inválido.');
        }

        [$headerPart, $payloadPart, $signaturePart] = $parts;

        $message = $headerPart . '.' . $payloadPart;

        $expectedSignature = hash_hmac(
            'sha256',
            $message,
            $this->secret(),
            true
        );

        $receivedSignature = $this->decode(
            $signaturePart
        );

        if (!hash_equals(
            $expectedSignature,
            $receivedSignature
        )) {
            throw new RuntimeException(
                'Firma del token inválida.'
            );
        }

        $header = json_decode(
            $this->decode($headerPart),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $payload = json_decode(
            $this->decode($payloadPart),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        if (
            !is_array($header) ||
            ($header['typ'] ?? null) !== 'JWT' ||
            ($header['alg'] ?? null) !== 'HS256'
        ) {
            throw new RuntimeException(
                'Algoritmo del token inválido.'
            );
        }

        if (
            !is_array($payload) ||
            !isset(
                $payload['sub'],
                $payload['role'],
                $payload['iat'],
                $payload['exp']
            ) ||
            !is_string($payload['sub']) ||
            $payload['sub'] === '' ||
            !in_array(
                $payload['role'],
                ['admin', 'promoter'],
                true
            ) ||
            !is_int($payload['iat']) ||
            !is_int($payload['exp']) ||
            $payload['iat'] > time() ||
            $payload['exp'] <= time() ||
            $payload['exp'] <= $payload['iat'] ||
            $payload['exp'] - $payload['iat']
                > self::TOKEN_DURATION
        ) {
            throw new RuntimeException(
                'Token inválido o expirado.'
            );
        }

        return $payload;
    }
}
