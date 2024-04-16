<?php

namespace App\Http\Controllers\Reel;

use App\Http\Controllers\Controller;
use App\Models\AdvertisementPath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    public function allVideo(){
        $allAdvertisement = AdvertisementPath::all();
        foreach($allAdvertisement as $advertisement){
            $manifestFilePath = storage_path($advertisement->hls_format_path . '/' . $advertisement->manifest_file_name);
            if($manifestFilePath){
                $manifestFile = Storage::get($manifestFilePath);
                return response($manifestFile)
                    ->header('Content-Type', 'application/vnd.apple.mpegurl') // Adjust the content type if needed
                    ->header('Content-Disposition', 'attachment; filename="manifest.m3u8"'); // Set filename with .m3u8 extension
            }
        }
    }

    




}
