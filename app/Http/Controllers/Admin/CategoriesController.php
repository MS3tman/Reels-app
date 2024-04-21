<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\DefaultCollection;
use App\Http\Resources\DefaultResource;
use App\Models\Category;
use App\Services\FileHandle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CategoriesController extends Controller
{
    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'category_title'=>'required|unique:categories',
            'image'=>'required'
        ]);
        if($validator->fails()){
            return $this->failure($validator->errors());
        }
        $newCategory = new Category();
        $newCategory->category_title = $request->category_title;
        $imagePath = (new FileHandle())->storeImage($request->image, 'category');
        $newCategory->image = $imagePath;
        $newCategory->save();
        return $this->success('successfully, category is created');
    }


    public function read(){
        $categories = Category::all();
        if ($categories->isEmpty()) {
            return $this->failure('Categories not found');
        }
        $data = [];
        foreach($categories as $category){
            $image = (new FileHandle())->retrieveFile($category->image, 'category');
            $data[] = [
                'id' => $category->id,
                'category_title'=>$category->category_title, 
                'image'=>$image
            ];
        }
        return new CategoryResource($data);
    }


    public function delete($id){
        $category = Category::find($id);
        if($category){
            $category->delete();
            return $this->success('category is deleted');
        }
        return $this->failure('category not found');
    }


}
