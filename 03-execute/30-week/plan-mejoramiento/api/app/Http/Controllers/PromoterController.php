<?php

namespace App\Http\Controllers;

use App\Application\RegisterPromoter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PromoterController extends BaseController
{
    public function __construct(private ?RegisterPromoter $register = null)
    {
        $this->register ??= app(RegisterPromoter::class);
    }

    public function store(Request $request): JsonResponse
    {
        $this->admin($request);
        $data = $request->validate([
            'username' => ['required', 'string', 'max:120'],
            'password' => ['required', 'string', 'min:12'],
            'role' => ['required', 'string', 'in:promoter'],
        ]);
        return response()->json($this->register->execute($data['username'], $data['password'], $data['role']), 201);
    }
}
