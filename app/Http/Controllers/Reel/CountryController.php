<?php

namespace App\Http\Controllers\Reel;

use App\Http\Controllers\Controller;
use App\Http\Resources\CountryCollection;
use App\Http\Resources\CountryResource;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function all(){
        $categories = Country::all();
        if($categories->isNotEmpty()){
            return new CountryCollection($categories);
        }
        return $this->failure('Not Found Countries');
    }


    public function filter($id){
        $category = Country::find($id);
        if($category->isNotEmpty()){
            return new CountryResource($category);
        }
        return $this->failure('Country is not Found');
    }
}
