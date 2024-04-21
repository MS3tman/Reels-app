<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReelsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id, 
            "username" => $this->user->full_name, 
            "title" => $this->title, 
            "target_url" => $this->target_url, 
            "target_views" => $this->target_views, 
            "price" => $this->price, 
            "offer_type" => $this->offer_type, 
            "offer" => $this->offer, 
            "video_manifest" => $this->video_manifest, 
            "status" => $this->status, 
            "created_at" => $this->created_at, 
            "updated_at" => $this->updated_at, 
            "clicks" => $this->clicks, 
            "comments_count" => $this->comments->count(), 
            //"comments" => $this->comments, 
            "countries_count" => $this->countries->count(), 
            "countries" => $this->countries->pluck('iso_code'), 
            "categories_count" => $this->categories->count(), 
            "categories" => $this->categories->pluck('category_title'), 
        ];
    }
}
