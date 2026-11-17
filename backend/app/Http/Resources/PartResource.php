<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Define cómo se "emplata" una pieza antes de mandarla al frontend.
 * Solo los campos que aparecen aquí salen en el JSON.
 *
 * @mixin \App\Models\Part
 */
class PartResource extends JsonResource
{
    /**
     * Convierte la pieza en un arreglo (que Laravel transforma a JSON).
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'name' => $this->name,
            'description' => $this->description,
            // Total calculado: suma del stock en todas las empresas (ver PartService).
            // Si la pieza no está en ninguna empresa, la suma llega como null: la mostramos como 0.
            'quantity' => (int) ($this->quantity ?? 0),
            'price' => $this->price,
            'active' => $this->active,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
