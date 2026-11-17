<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Part;
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

    /**
     * Pone o cambia el stock de una pieza en una empresa.
     * Si la pieza no estaba en la empresa, la agrega.
     *
     * @param  array<string, mixed>  $data  ['stock' => ..., 'min_stock' => ...]
     */
    public function setStock(Company $company, Part $part, array $data): Company
    {
        $company->parts()->syncWithoutDetaching([$part->id => $data]);

        return $this->find($company);
    }

    /**
     * Quita una pieza de una empresa (borra su fila en company_part).
     */
    public function removePart(Company $company, Part $part): void
    {
        $company->parts()->detach($part->id);
    }
}
