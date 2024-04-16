<?php

namespace App\Http\Controllers\Reel;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function all(){
        $categories = Category::all();
        if($categories->isNotEmpty()){
            return new CategoryCollection($categories);
        }
        return $this->failure('Not Found Categories');
    }


    public function filter($id){
        $category = Category::find($id);
        if($category->isNotEmpty()){
            return new CategoryResource($category);
        }
        return $this->failure('Category is not Found');
    }
}
