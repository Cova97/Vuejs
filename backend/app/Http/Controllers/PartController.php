<?php

namespace App\Http\Controllers;

use App\Models\Part;
use Illuminate\Http\Request;
use App\Http\Requests\StorePartRequest;
use App\Http\Requests\UpdatePartRequest;

class PartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Part::orderBy('name')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePartRequest $request)
    {
        $part = Part::create($request->validated());

        return response()->json($part, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Part $part)
    {
        return $part;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePartRequest $request, Part $part)
    {
        $part->update($request->validated());

        return $part;
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
