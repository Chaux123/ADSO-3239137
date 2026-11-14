
<?php

namespace App\Http\Controllers;

use App\Domain\Reference;
use App\Domain\ToyRules;
use App\Exceptions\ApiException;
use App\Support\JsonData;
use App\Support\Presenters;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

final class ToyController extends BaseController
{
    private function query()
    {
        return DB::table('toys')
            ->join(
                'categories',
                'categories.id',
                '=',
                'toys.category_id'
            )
            ->whereNull('toys.deleted_at')
            ->select(
                'toys.*',
                'categories.name as category_name'
            );
    }

    private function find(string $id)
    {
        $toy = $this->query()
            ->where('toys.id', $id)
            ->first();

        if (!$toy) {
            throw new ApiException(
                'NOT_FOUND',
                'Juguete no encontrado.',
                404
            );
        }

        return $toy;
    }

    // GET /api/v1/categories
    public function categories()
    {
        return response()->json(
            DB::table('categories')
                ->orderBy('name')
                ->get()
        );
    }

    // GET /api/v1/toys
    public function index(Request $request)
    {
        $data = $request->validate([
            'q' => 'sometimes|string|max:250',
            'category_id' => 'sometimes|uuid',
        ]);

        $query = $this->query();

        if (!empty($data['q'])) {
            $term = '%' . $data['q'] . '%';

            $query->where(function ($search) use ($term) {
                $search->where(
                    'toys.name',
                    'ilike',
                    $term
                )->orWhere(
                    'toys.brand',
                    'ilike',
                    $term
                );
            });
        }

        if (!empty($data['category_id'])) {
            $query->where(
                'toys.category_id',
                $data['category_id']
            );
        }

        return response()->json(
            JsonData::paged(
                $query->orderBy('toys.name')
                    ->orderBy('toys.id'),
                $request,
                fn ($row) => Presenters::toy($row)
            )
        );
    }

    // GET /api/v1/toys/{id}
    public function show(string $id)
    {
        return response()->json(
            Presenters::toy($this->find($id))
        );
    }

    // POST /api/v1/toys
    public function store(Request $request)
    {
        $this->admin($request);

        $data = $request->validate([
            'name' => 'required|string|max:250',
            'brand' => 'required|string|max:200',
            'reference' => 'required|string|max:50',
            'category_id' => 'required|uuid|exists:categories,id',
            'total_units' => 'required|integer|min:1',
        ]);

        try {
            $reference = Reference::normalize(
                $data['reference']
            );
        } catch (InvalidArgumentException $exception) {
            throw new ApiException(
                'VALIDATION_ERROR',
                $exception->getMessage(),
                422
            );
        }

        $exists = DB::table('toys')
            ->where('reference', $reference)
            ->whereNull('deleted_at')
            ->exists();

        if ($exists) {
            throw new ApiException(
                'REFERENCE_TAKEN',
                'La referencia ya existe.',
                409
            );
        }

        $id = (string) Str::uuid();

        try {
            DB::table('toys')->insert([
                'id' => $id,
                'name' => trim($data['name']),
                'brand' => trim($data['brand']),
                'reference' => $reference,
                'category_id' => $data['category_id'],
                'total_units' => $data['total_units'],
                'available_units' => $data['total_units'],
                'cover_key' => null,
                'deleted_at' => null,
            ]);
        } catch (QueryException $exception) {
            if ($exception->getCode() === '23505') {
                throw new ApiException(
                    'REFERENCE_TAKEN',
                    'La referencia ya existe.',
                    409
                );
            }

            throw $exception;
        }

        return response()->json(
            Presenters::toy($this->find($id)),
            201
        );
    }

    // PUT /api/v1/toys/{id}
    public function update(Request $request, string $id)
    {
        $this->admin($request);

        $data = $request->validate([
            'name' => 'required|string|max:250',
            'brand' => 'required|string|max:200',
            'category_id' => 'required|uuid|exists:categories,id',
            'total_units' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($id, $data) {
            $toy = DB::table('toys')
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->lockForUpdate()
                ->first();

            if (!$toy) {
                throw new ApiException(
                    'NOT_FOUND',
                    'Juguete no encontrado.',
                    404
                );
            }

            try {
                $available = ToyRules::changeTotal(
                    $toy->total_units,
                    $toy->available_units,
                    $data['total_units']
                );
            } catch (InvalidArgumentException $exception) {
                throw new ApiException(
                    'VALIDATION_ERROR',
                    $exception->getMessage(),
                    422
                );
            }

            DB::table('toys')
                ->where('id', $id)
                ->update([
                    'name' => trim($data['name']),
                    'brand' => trim($data['brand']),
                    'category_id' => $data['category_id'],
                    'total_units' => $data['total_units'],
                    'available_units' => $available,
                ]);
        });

        return response()->json(
            Presenters::toy($this->find($id))
        );
    }

    // DELETE /api/v1/toys/{id}
    public function destroy(Request $request, string $id)
    {
        $this->admin($request);

        DB::transaction(function () use ($id) {
            $toy = DB::table('toys')
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->lockForUpdate()
                ->first();

            if (!$toy) {
                throw new ApiException(
                    'NOT_FOUND',
                    'Juguete no encontrado.',
                    404
                );
            }

            $hasOpenLoans = DB::table('loan_items')
                ->where('toy_id', $id)
                ->whereNull('returned_at')
                ->exists();

            if ($hasOpenLoans) {
                throw new ApiException(
                    'TOY_HAS_OPEN_LOANS',
                    'No puede retirar un juguete con préstamos abiertos.',
                    409
                );
            }

            DB::table('toys')
                ->where('id', $id)
                ->update([
                    'deleted_at' => now('UTC'),
                ]);
        });

        return response()->noContent();
    }

    // PUT /api/v1/toys/{id}/cover
    public function putCover(Request $request, string $id)
    {
        $this->admin($request);
        $this->find($id);

        $data = $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        $file = $data['file'];
        $mime = $file->getMimeType();

        if (!in_array($mime, ['image/jpeg', 'image/png'], true)) {
            throw new ApiException(
                'VALIDATION_ERROR',
                'Solo se permiten imágenes JPG o PNG.',
                422
            );
        }

        $key = (string) Str::uuid()
            . ($mime === 'image/png' ? '.png' : '.jpg');

        Storage::disk('local')->put(
            'covers/' . $key,
            file_get_contents($file->getRealPath())
        );

        $oldKey = DB::table('toys')
            ->where('id', $id)
            ->value('cover_key');

        DB::table('toys')
            ->where('id', $id)
            ->update([
                'cover_key' => $key,
            ]);

        if ($oldKey) {
            Storage::disk('local')->delete(
                'covers/' . $oldKey
            );
        }

        return response()->json([
            'cover_key' => $key,
        ]);
    }

    // GET /api/v1/toys/{id}/cover
    public function cover(string $id)
    {
        $toy = $this->find($id);

        if (
            !$toy->cover_key ||
            !Storage::disk('local')->exists(
                'covers/' . $toy->cover_key
            )
        ) {
            throw new ApiException(
                'NOT_FOUND',
                'Imagen no encontrada.',
                404
            );
        }

        $type = str_ends_with(
            $toy->cover_key,
            '.png'
        ) ? 'image/png' : 'image/jpeg';

        return response(
            Storage::disk('local')->get(
                'covers/' . $toy->cover_key
            )
        )
            ->header('Content-Type', $type)
            ->header('Cache-Control', 'private, max-age=3600');
    }

    // DELETE /api/v1/toys/{id}/cover
    public function deleteCover(Request $request, string $id)
    {
        $this->admin($request);

        $toy = $this->find($id);

        DB::table('toys')
            ->where('id', $id)
            ->update([
                'cover_key' => null,
            ]);

        if ($toy->cover_key) {
            Storage::disk('local')->delete(
                'covers/' . $toy->cover_key
            );
        }

        return response()->noContent();
    }
}
