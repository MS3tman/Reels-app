<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CountryCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request);
        return $this->collection->map(function ($countries) {
            return [
                'id' => $countries->id,
                'name_en'=>$countries->name_en,
                'dial_code'=>$countries->dial_code,
                'phone_length'=>$countries->phone_length,
                'iso_code'=>$countries->iso_code,
                // Add other properties as needed
            ];
        })->all();
    }
}
