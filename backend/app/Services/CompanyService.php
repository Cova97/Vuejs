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
}