<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePartRequest;
use App\Http\Requests\UpdatePartRequest;
use App\Http\Resources\PartResource;
use App\Models\Part;
use App\Services\PartService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class PartController extends Controller
{
    /**
     * Laravel crea el PartService y nos lo "inyecta" automáticamente.
     * Igual que en NestJS: constructor(private readonly partsService: PartsService) {}
     */
    public function __construct(
        private readonly PartService $parts,
    ) {}

    /**
     * GET /api/parts
     */
    public function index(): AnonymousResourceCollection
    {
        return PartResource::collection($this->parts->list());
    }

    /**
     * POST /api/parts
     */
    public function store(StorePartRequest $request): PartResource
    {
        $part = $this->parts->create($request->validated());

        // Laravel detecta que la pieza es nueva y responde con 201 (Created).
        return new PartResource($part);
    }

    /**
     * GET /api/parts/{part}
     */
    public function show(Part $part): PartResource
    {
        return new PartResource($part);
    }

    /**
     * PUT/PATCH /api/parts/{part}
     */
    public function update(UpdatePartRequest $request, Part $part): PartResource
    {
        $part = $this->parts->update($part, $request->validated());

        return new PartResource($part);
    }

    /**
     * DELETE /api/parts/{part}
     */
    public function destroy(Part $part): Response
    {
        $this->parts->delete($part);

        return response()->noContent();
    }
}
