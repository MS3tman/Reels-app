<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Resources\CountryResource;
use App\Http\Resources\CategoryResource;

class HomeController extends Controller
{
    public function CountriesList(Request $request){
        $categories = Country::all();
        return CountryResource::collection($categories);
    }

    public function CategoriesList(Request $request){
        $categories = Category::all();
        return CategoryResource::collection($categories);
    }
}
