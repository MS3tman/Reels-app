<?php
namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Storage;


class FileHandle
{
    public function storeImage($image, $location){
           $imageData = base64_decode($image);
           // Generate a unique filename
           //$imagePath = time() . uniqid() . '.jpg';
           $extension = 'jpg'; // Assuming default extension is jpg, you can change this as needed
           $imagePath = Carbon::now()->toDateString() . "-" . uniqid() . "." . $extension;
           // Store the image data to the specified disk and directory with a unique filename
           //$imageData->storePubliclyAs('public/'.$location, $imagePath);
           Storage::disk('public')->put($location . '/' . $imagePath, $imageData);
           return $imagePath;
    }


    public function storeVideo($video, $location){
       $videoData = base64_decode($video);
       // Generate a unique filename
       $extension = $video->getClientOriginalExtension();
       $videoPath = time() . uniqid() . '.' . $extension;
       // Store the image data to the specified disk and directory with a unique filename
       Storage::disk('public')->put($location .'/' . $videoPath, $videoData);
       return $videoPath;
   }


    public function retrieveFile($filePath, $location){
        // Check if the image exists in storage
        if (Storage::exists('public/' . $location.'/' . $filePath)) {
            // Retrieve the image file contents
            $fileData = Storage::disk('public')->get($location.'/' . $filePath);
            // Return the compressed image data
           // Encode the image data as base64
            $compressedFileData = base64_encode($fileData);
            return $compressedFileData;
        } else {
            // If the image does not exist, return null or handle the error accordingly
            return null;
        }
    }

}