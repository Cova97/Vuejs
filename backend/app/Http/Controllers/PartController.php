<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePartRequest;
use App\Http\Requests\UpdatePartRequest;
use App\Http\Resources\PartResource;
use App\Models\Part;

class PartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return PartResource::collection(Part::orderBy('name')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePartRequest $request)
    {
        $part = Part::create($request->validated());

        // Laravel detecta que la pieza es nueva y responde con 201 (Created).
        return new PartResource($part);
    }

    /**
     * Display the specified resource.
     */
    public function show(Part $part)
    {
        return new PartResource($part);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePartRequest $request, Part $part)
    {
        $part->update($request->validated());

        return new PartResource($part);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Part $part)
    {
        $part->delete();

        return response()->noContent();
    }
}
