<?php
namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Storage;


class FileHandle
{
    public function storeImage($image, $location){
           $imageData = base64_decode($image);
           $imagePath = Carbon::now()->toDateString() . "-" . uniqid() . ".jpg";
           Storage::disk('public')->put($location . '/' . $imagePath, $imageData);
           return $imagePath;
    }


    public function retrieveFile($filePath, $location){
        if (Storage::exists("public/{$location}/{$filePath}")) {
            $imagePath = url(Storage::url("app/{$location}/{$filePath}"));
            return $imagePath;
        } else {
            return null;
        }
    }

}
