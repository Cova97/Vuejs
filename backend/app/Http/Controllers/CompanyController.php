<?php

namespace App\Http\Controllers;

use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Services\CompanyService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CompanyController extends Controller
{
    public function __construct(
        private readonly CompanyService $companies,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return CompanyResource::collection($this->companies->list());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company): CompanyResource
    {
        // Laravel ya buscó la empresa por el {company} de la URL (o respondió 404 si no existe).
        return new CompanyResource($this->companies->find($company));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
