<?php
namespace App\Http\Controllers;

use App\Adapters\FamilyRepository;
use App\Application\RegisterFamily;
use App\Exceptions\ApiException;
use App\Support\JsonData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class FamilyController extends BaseController
{
    public function __construct(private FamilyRepository $families) {}

    public function index(Request $request): JsonResponse
    {
        $data = $request->validate(['q' => ['sometimes', 'string', 'max:200']]);
        return response()->json(JsonData::paged($this->families->search(trim($data['q'] ?? '')), $request, fn ($row) => $row));
    }

    public function show(string $id): JsonResponse
    {
        $family = $this->families->find($id);
        if (!$family) throw new ApiException('NOT_FOUND', 'Familia no encontrada.', 404);
        return response()->json([
            'id' => $family->id,
            'full_name' => $family->full_name,
            'document_number' => $family->document_number,
            'active_loans' => $this->families->pendingItems($id),
        ]);
    }

    public function store(Request $request, RegisterFamily $register): JsonResponse
    {
        $data = $request->validate(['full_name' => ['required', 'string', 'max:200'], 'document_number' => ['required', 'string', 'max:30']]);
        return response()->json($register->execute($data['full_name'], $data['document_number']), 201);
    }
}
