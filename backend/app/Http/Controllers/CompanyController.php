<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Requests\UpdateStockRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Models\Part;
use App\Services\CompanyService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

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
    public function store(StoreCompanyRequest $request): CompanyResource
    {
        $company = $this->companies->create($request->validated());

        return new CompanyResource($company);
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
    public function update(UpdateCompanyRequest $request, Company $company): CompanyResource
    {
        $company = $this->companies->update($company, $request->validated());

        return new CompanyResource($company);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company): Response
    {
        $this->companies->delete($company);

        // 204 No Content: "listo, lo borré" (sin cuerpo en la respuesta).
        return response()->noContent();
    }

    /**
     * PUT /api/companies/{company}/parts/{part}
     * Pone o cambia el stock de la pieza en la empresa.
     */
    public function updateStock(UpdateStockRequest $request, Company $company, Part $part): CompanyResource
    {
        $company = $this->companies->setStock($company, $part, $request->validated());

        return new CompanyResource($company);
    }

    /**
     * DELETE /api/companies/{company}/parts/{part}
     * Quita la pieza de la empresa (la pieza sigue existiendo).
     */
    public function removePart(Company $company, Part $part): Response
    {
        $this->companies->removePart($company, $part);

        return response()->noContent();
    }
}
