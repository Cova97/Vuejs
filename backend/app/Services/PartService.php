<?php

namespace App\Services;

use App\Models\Part;
use Illuminate\Database\Eloquent\Collection;

/**
 * Lógica de negocio de las piezas.
 *
 * El controlador solo recibe la petición y devuelve la respuesta;
 * todo lo que es "qué hacer con las piezas" vive aquí.
 * (Es el equivalente a un @Injectable() PartsService en NestJS).
 */
class PartService
{
    /**
     * Todas las piezas ordenadas por nombre.
     *
     * @return Collection<int, Part>
     */
    public function list(): Collection
    {
        // withSum agrega a cada pieza un campo "quantity" con la suma
        // del stock que tiene en todas las empresas (tabla company_part).
        return Part::withSum('companies as quantity', 'company_part.stock')
            ->orderBy('name')
            ->get();
    }

    /**
     * Calcula el total (quantity) de una sola pieza ya cargada.
     */
    public function withQuantity(Part $part): Part
    {
        return $part->loadSum('companies as quantity', 'company_part.stock');
    }

    /**
     * Crea una pieza con datos ya validados.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Part
    {
        return $this->withQuantity(Part::create($data));
    }

    /**
     * Actualiza una pieza existente y la devuelve.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Part $part, array $data): Part
    {
        $part->update($data);

        return $this->withQuantity($part);
    }

    /**
     * Borra una pieza.
     */
    public function delete(Part $part): void
    {
        $part->delete();
    }
}
