<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

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
            "username" => $this->reel->user->full_name, 
            "title" => $this->reel->title, 
            "target_url" => $this->reel->target_url, 
            "target_views" => $this->target_views, 
            "price" => view_price(), 
            "copoun_code" => $this->copoun_code, 
            "copoun_per" => $this->copoun_per, 
            //"video_manifest" => $this->reel->video_manifest, 
            "video_manifest" => url(Storage::url("app/{$this->reel->video_manifest}/master.m3u8")),
            "status" => reel_status($this->status), 
            "status_color" => reel_status($this->status, 'color'), 
            "created_at" => $this->created_at, 
            "updated_at" => $this->updated_at, 
            "clicks" => $this->clicks, 
            "views" => $this->views()->sum('count'), 
            // "comments_count" => $this->comments->count(), 
            //"comments" => $this->comments, 
            "countries_count" => $this->reel->countries->count(), 
            "countries" => $this->reel->countries->pluck('iso_code'), 
            "categories_count" => $this->reel->categories->count(), 
            "categories" => $this->reel->categories->pluck('name'), 
        ];
    }
}
