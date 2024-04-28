<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CountryResource;
use App\Http\Resources\DefaultCollection;
use App\Http\Resources\DefaultResource;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CountriesController extends Controller
{
    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'name'=>'required',
            'dial_code'=>'required',
            'phone_length'=>'required',
            'iso_code'=>'required',
        ]);
        if($validator->fails()){
            return $this->failure($validator->errors());
        }
        $newCountry = new Country();
        $newCountry->name = $request->name;
        $newCountry->dial_code = $request->dial_code;
        $newCountry->phone_length = $request->phone_length;
        $newCountry->iso_code = $request->iso_code;
        $newCountry->save();
        return $this->success('successfully, country is created');
    }


    public function read(){
        $countries = Country::all();
        if(!$countries){
            return $this->failure('not found');
        }
        return new CountryResource($countries);
    }


    public function delete($id){
        $country = Country::find($id);
        if($country){
        $country->delete();
        return $this->success('Successfully, country is deleted');
        }
        return $this->failure('country not found');
    }

}
