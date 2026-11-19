<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\Api\Concerns\MasterDataHelpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Support\Slugger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

/** Dasar untuk master data bernama + slug + is_active (kategori peserta dan kategori pengunjung). */
abstract class SimpleCategoryController extends Controller
{
    use MasterDataHelpers;

    /** @return class-string<Model> */
    abstract protected function model(): string;

    abstract protected function table(): string;

    /** Kategori ini sudah dipakai data lain? Kalau ya, tidak boleh dihapus. */
    abstract protected function isUsed(Model $category): bool;

    protected function hasDescription(): bool
    {
        return false;
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = $this->onlyVisible(($this->model())::query(), $request)->orderBy('name');

        return CategoryResource::collection($query->get());
    }

    public function show(Request $request, string $id): CategoryResource
    {
        $category = ($this->model())::query()->findOrFail($id);
        $this->ensureVisible($request, $category);

        return new CategoryResource($category);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->rules());
        $data['slug'] = $data['slug'] ?? Slugger::unique($this->model(), $data['name']);

        $category = ($this->model())::create($data)->refresh();

        return (new CategoryResource($category))->response()->setStatusCode(201);
    }

    public function update(Request $request, string $id): CategoryResource
    {
        $category = ($this->model())::query()->findOrFail($id);
        $category->fill($request->validate($this->rules($category)))->save();

        return new CategoryResource($category);
    }

    public function destroy(string $id): JsonResponse
    {
        $category = ($this->model())::query()->findOrFail($id);

        if ($this->isUsed($category)) {
            return $this->inUse('Kategori sudah dipakai data lain. Nonaktifkan saja, jangan dihapus.');
        }

        $category->delete();

        return response()->json(['message' => 'Kategori dihapus.']);
    }

    /** @return array<string, array<int, mixed>> */
    private function rules(?Model $category = null): array
    {
        $sometimes = $category ? ['sometimes'] : [];

        $rules = [
            'name' => [...$sometimes, 'required', 'string', 'max:100', Rule::unique($this->table(), 'name')->ignore($category?->getKey())],
            'slug' => [$category ? 'sometimes' : 'nullable', 'string', 'max:100', 'alpha_dash', Rule::unique($this->table(), 'slug')->ignore($category?->getKey())],
            'is_active' => [...$sometimes, 'boolean'],
        ];

        if ($this->hasDescription()) {
            $rules['description'] = ['nullable', 'string', 'max:1000'];
        }

        return $rules;
    }
}
