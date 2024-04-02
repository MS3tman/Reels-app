<?php

namespace App\Http\Controllers;

use App\Models\AdvertisementPath;
use Illuminate\Http\Request;

class DownloadController extends Controller
{
    public function allVideo(){
        $allAdvertisement = AdvertisementPath::all();
        foreach($allAdvertisement as $advertisement){
            if(storage_path($advertisement->hls_format_path . '/' . $advertisement->manifest_file_name));
        }
    }
}
