<?php

namespace App\Http\Resources;

use App\Services\FileHandle;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CategoryCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(function ($category) {
            return [
                'id' => $category->id,
                'category_title' => $category->category_title,
                'image' => (new FileHandle())->retrieveFile($category->image, 'category'),
                // Add other properties as needed
            ];
        })->all();
    }
}
