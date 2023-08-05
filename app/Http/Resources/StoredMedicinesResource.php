<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StoredMedicinesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->resource->map(function ($medicine) {
            return [
                'id' => $medicine->id,
                'quantity' => $medicine->quantity,
                'price' => $medicine->price,
                'brand_name' => $medicine->drug!=null?$medicine->drug->brand_name:'',
            ];
        });
    }
}
