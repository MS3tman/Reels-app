<?php
namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Storage;


class FileHandle
{
    public function storeImage($image, $location){
           $imageData = base64_decode($image);
           $extension = 'jpg'; // Assuming default extension is jpg, you can change this as needed
           $imagePath = Carbon::now()->toDateString() . "-" . uniqid() . "." . $extension;
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
