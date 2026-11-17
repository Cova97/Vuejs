<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
        'id'      => $this->id,
        'name'    => $this->name,
        'address' => $this->address,
        'phone'   => $this->phone,
        'rfc'     => $this->rfc,
        'parts'   => $this->whenLoaded('parts', fn () => $this->parts->map(fn ($part) => [
            'id'        => $part->id,
            'sku'       => $part->sku,
            'name'      => $part->name,
            'stock'     => $part->pivot->stock,
            'min_stock' => $part->pivot->min_stock,
            ])),
        ];

    }
}
