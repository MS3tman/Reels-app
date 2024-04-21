<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use App\Services\FileHandle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\UserRegister;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    protected function getToken(User $user) {
        return $user->createToken($user->email)->plainTextToken;
    }

    protected function getUserData(User $userData){
        $image = (new FileHandle())->retrieveFile($userData->image_path, 'user');
        $finalUserData = [
            'id'=>$userData->id,
            'full_name'=>$userData->full_name,
            'email'=>$userData->email,
            'country_code'=>$userData->country_code,
            'phone_number'=>$userData->phone_number,
            'image'=>$image,
            'address'=>$userData->address,
            'token'=>$userData->remember_token,
            'token'=>$this->getToken($userData),
        ];
        return $finalUserData;
    }

    protected function register(Request $request) {
        //User::truncate();
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
            return $this->failure('Some required fileds is missing.', $validator->errors()->all());
        }
        $new                    = new User;
        $new->full_name         = $request->full_name;
        $new->email             = $request->email;
        $new->country_code      = $request->country_code;
        $new->phone_number      = $request->phone_number;
        $new->address           = $request->address;
        $new->phone_number      = $request->phone_number;
        $new->password          = Hash::make($request->password);
        $new->remember_token    = Str::random(60);
        $new->active            = false;
        $new->vtoken            = rand(10000, 99999);
        if($request->has('image')){
            $imagePath = (new FileHandle())->storeImage($request->image, 'user');
            $new->image_path = $imagePath;
        }
        if($new->save()) {
            $new->verify_link = route('verify_register', ['token' => $new->remember_token]);
            //Send Email
            Mail::to($new->email)->send(new UserRegister($new, 'Activate your account.', 'register'));
            return $this->success('Done Successfully, Please check your email.', [
                'verify_link' => $new->verify_link 
            ]);
        }
        return $this->failure('Something wrong, Please try again later');
    }

    protected function verifyRegister(Request $request, $token) {
        $validator = Validator::make($request->all(), [
            'code' => 'required|size:5',
        ]);
        if($validator->fails()){
            return $this->failure('Invalid given code.');
        }
        $user = User::where(['remember_token' => $token, 'vtoken' => $request->code])->first();
        if(!empty($user)){
            $user->remember_token    = '';
            $user->active            = true;
            $user->vtoken            = '';
            $user->update();
            return $this->success('The account has been successfully updated.', $this->getUserData($user));
        }
        return $this->failure('Invalid given code.');
    }

    protected function retryRegister(Request $request, $token) {

        $user = User::where(['remember_token' => $token])->first();
        if(empty($user)){
            return $this->failure('Invalid Token.');
        }
        $user->vtoken            = rand(10000, 99999);
        $user->update();
        $user->verify_link = route('verify_register', ['token' => $user->remember_token]);
        //Send Email
        Mail::to($user->email)->send(new UserRegister($user, 'Activate your account.', 'register'));
        return $this->success('Done Successfully, Please check your email.', [
            'verify_link' => $user->verify_link 
        ]);
    }

    protected function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if($validator->fails()){
            return $this->failure('Email\Password is invalid.');
        }
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'active' => true])) {
            $user = Auth::user();
            $user->remember_token    = null;
            $user->vtoken            = null;
            $user->update();
            return $this->success('Login successful.', $this->getUserData($user));
        }
        return $this->failure('Email\Password is invalid.');
    }

    protected function logout() {
        $user = Auth::user();
        $user->tokens()->delete();
        return $this->success('logout happen successful.');
    }

    protected function forgetPassword(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
        if($validator->fails()){
            return $this->failure('Email Filed is Required.');
        }
        $user = User::where('email', $request->email)->first();
        if(empty($user)){
            return $this->failure('Email Not Exists.');
        }
        if($user->active == false){
            return $this->failure('Your Email is not active yet.');
        }

        $user->remember_token    = Str::random(60);
        $user->vtoken            = rand(10000, 99999);
        $user->update();
        //Send Email
        Mail::to($user->email)->send(new UserRegister($user, 'Password Reset Request.', 'forget'));
        return $this->success('Please check your email to reset the password.');
    }

    protected function resetPassword(Request $request, $token) {
        $validator = Validator::make($request->all(), [
            'code' => 'required|size:5',
            'password' => 'required|min:8',
        ]);
        if($validator->fails()){
            return $this->failure('Some required fileds is missing.', $validator->errors()->all());
        }
        $user = User::where(['remember_token' => $token, 'vtoken' => $request->code])->first();
        if(!empty($user)){
            $user->remember_token    = '';
            $user->active            = true;
            $user->vtoken            = '';
            $user->password            = Hash::make($request->password);
            $user->update();
            // Send Email
            Mail::to($user->email)->send(new UserRegister($user, 'Password Successfully Reset.', 'reset'));
            return $this->success('The password has been successfully updated.');
        }
        return $this->failure('Invalid given code.');
    }
}
