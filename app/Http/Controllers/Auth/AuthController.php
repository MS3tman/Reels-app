<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Services\FileHandle;
use Illuminate\Http\Request;
use App\Services\TwilioService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function getUserData(User $user){
        $userData = User::where('id', $user->id)->first();
        $image = (new FileHandle())->retrieveFile($userData->image_path, 'user');
        $token = $user->createToken($user->phone_number)->plainTextToken;
        $finalUserData = [
            'id'=>$userData->id,
            'full_name'=>$userData->full_name,
            'email'=>$userData->email,
            'country_code'=>$userData->country_code,
            'phone_number'=>$userData->phone_number,
            'image'=>$image,
            'address'=>$userData->address,
            'token'=>$token,
        ];
        return $finalUserData;
    }


    public function verifyPhoneNumberForRegister(Request $request){
        $validator = Validator::make($request->all(), [
            'country_code' => 'required',
            'phone_number' => 'required|unique:users'
        ]);
        if($validator->fails()){
            return response()->json(['errors'=>$validator->errors()], 403);
        }

        $verify = (new TwilioService())->sendMessage($request->country_code.$request->phone_number);
        if($verify['status'] == 'pending'){
            return response()->json(['status'=>'awaiting verify']);
        }else{
            return response()->json(['verify'=>$verify],$verify['status']);
        }
    }


    public function verifyPhoneNumber(Request $request){
        $validator = Validator::make($request->all(), [
            'country_code' => 'required',
            'phone_number' => 'required'
        ]);

        if($validator->fails()){
            return response()->json(['errors'=>$validator->errors()], 403);
        }

        $checkPhone = User::where('phone_number', $request->phone_number)->where('active', true)->first();
        if(!$checkPhone){
            return response()->json(['errors'=>'Phone Number is Not Found, Please make sure you have entered the correct phone number'], 404);
        }

        $verify = (new TwilioService())->sendMessage($request->country_code.$request->phone_number);
        if($verify['status'] == 'pending'){
            return response()->json(['status'=>'awaiting verify']);
        }else{
            return response()->json(['verify'=>$verify],$verify['status']);
        }
    }


    // for verify OTP code with return token and user data.
    public function verifyOtpWithToken(Request $request){
        $validator = Validator::make($request->all(), [
            'country_code' => 'required',
            'phone_number' => 'required',
            'pin' => 'required|min:6'
        ]);
        if($validator->fails()){
            return response()->json(['errors'=>$validator->errors()], 403);
        }

        $validate = (new TwilioService())->verify($request->country_code.$request->phone_number,$request->pin);
        if($validate['status'] == 'approved'){
            $user = User::where('phone_number', $request->phone_number)->where('active', true)->first();
            $userData = $this->getUserData($user);    
            return response()->json(['message'=>'successfull OPT code is valid', 'data'=>$userData], 200);
        }
        return response()->json(['errors'=>'Invalid PIN code','message'=>$validate], $validate['status']);
    }


    // for verify OTP code without return token or user data.
    public function verifyOtpWithoutToken(Request $request){
        $validator = Validator::make($request->all(), [
            'country_code' => 'required',
            'phone_number' => 'required',
            'pin' => 'required|min:6'
        ]);
        if($validator->fails()){
            return response()->json(['errors'=>$validator->errors()], 403);
        }

        $validate = (new TwilioService())->verify($request->country_code.$request->phone_number,$request->pin);
        if($validate['status'] == 'approved'){
            return response()->json(['message'=>'successfully OPT code is valid'], 200);
        }
        return response()->json(['errors'=>'Invalid PIN code','message'=>$validate], $validate['status']);
    }


    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'full_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'country_code' => 'required',
            'phone_number' => 'required|unique:users',
            'address' => '',
            'image' => '',
        ]);
        if($validator->fails()){
            return response()->json(['errors'=>$validator->errors()], 403);
        }

        $newUser = new User();
        $newUser->full_name = $request->full_name;
        $newUser->email = $request->email;
        $newUser->password = Hash::make($request->password);
        $newUser->country_code = $request->country_code;
        $newUser->phone_number = $request->phone_number;
        $newUser->active = false;
        $newUser->vtoken = rand(10000, 99999);
        if($request->has('image')){
            $imagePath = (new FileHandle())->storeImage($request->image, 'user');
            $newUser->image_path = $imagePath;
        }
        if($request->has('address')){
            $newUser->address = $request->address;
        }
        $newUser->save();
        
        $userData = $this->getUserData($newUser);
        return response()->json(['message'=>'successfully user account is created', 'data'=>$userData], 200);
    }


    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);
        if($validator->fails()){
            return response()->json(['errors'=>$validator->errors()]);
        }
        $user = User::where('email', $request->email)->where('active', true)->first();
        if ($user) {
            $credentials = $request->only('email', 'password');
            if (Auth::attempt($credentials)) {
                $user = Auth::loginUsingId($user->id); 
                $userData = $this->getUserData($user);
                return response()->json(['message'=>'Login Successfully', 'data'=>$userData], 200);
            }
            return response()->json(['errors'=>'The password is incorrect, Login is faild'], 403);
        }
        return response()->json(['errors'=>'Email is Not Found!'], 404);
        
    }


    public function resetPassword(Request $request){
        $validator = Validator::make($request->all(), [
            'phone_number'=>'required',
            'password' => 'required|min:8',
        ]);
        if($validator->fails()){
            return response()->json(['errors'=>$validator->errors()], 400);
        }

        $user = User::where('phone_number', $request->phone_number)->where('active', true)->first();
        if($user){
            $user->password = bcrypt($request->password);
            $user->save();
            return response()->json(['message'=>'successfully your password is updated'], 200);
        }
        return response()->json(['errors'=>'user is not found!'], 404);
    }


    public function logout(Request $request){
        if (!$request->user()) {
            return response()->json(['errors' => 'User not authenticated'], 401);
        }
        $request->user()->currentAccessToken()->delete(); // Delete only the current token
        return response()->json(['message' => 'Logged out successfully'], 200);
    }

}
