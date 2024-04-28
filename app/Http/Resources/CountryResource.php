<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request);
        return [
            'id' => $this->id,
            'name'=>$this->name,
            'dial_code'=>$this->dial_code,
            'phone_length'=>$this->phone_length,
            'iso_code'=>$this->iso_code,
        ];
    }
}
