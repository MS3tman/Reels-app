<?php

namespace App\Http\Resources;

use App\Services\FileHandle;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'id'=>$this->id,
            'category_title'=>$this->category_title,
            'image'=>(new FileHandle())->retrieveFile($this->image, 'category')
        ];
    }
}
