<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Database\Eloquent\Collection;

class CompanyService
{
    /**
     * Todas las empresas ordenadas por nombre.
     *
     * @return Collection<int, Company>
     */
    public function list(): Collection
    {
        return Company::orderBy('name')->get();
    }

    /**
     * Una empresa con sus piezas (y el stock de cada una) ya cargadas.
     */
    public function find(Company $company): Company
    {
        return $company->load('parts');
    }

    public function create(array $data): Company
    {
        return Company::create($data);
    }

    /**
     * Actualiza una empresa y la devuelve.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Company $company, array $data): Company
    {
        $company->update($data);

        return $company;
    }

    /**
     * Borra una empresa (y por cascada, sus filas de stock en company_part).
     */
    public function delete(Company $company): void
    {
        $company->delete();
    }
}