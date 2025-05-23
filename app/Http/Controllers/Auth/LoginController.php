<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\User;
use App\Mail\UserRegister;
use Illuminate\Support\Str;
use App\Services\FileHandle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
            'bdate'=>$userData->bdate,
            'gander'=>$userData->gander,
            'token'=>$this->getToken($userData),
        ];
        return $finalUserData;
    }

    protected function register(Request $request) {
        //User::truncate();
        $validator = Validator::make($request->all(), [
            'full_name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'bdate' => 'nullable|date',
            'gender' => 'nullable|in:m,f',
            'country_code' => 'required',
            'phone_number' => 'required|unique:users',
            'address.lat' => 'nullable|numeric',
            'address.lat' => 'nullable|numeric',
        ]);
        if($validator->fails()){
            return $this->failure('Some required fileds is missing.', $validator->errors()->all());
        }
        $checkUser = User::where('email', $request->email)->first();
        if(!empty($checkUser)){
            if($checkUser->active == true){
                return $this->failure('The email is already exists.');
            }else{
                $new = $checkUser;
                $do = 'update';
            }
        }else{
            $new                    = new User;
            $do = 'save';
        }
        $new->full_name         = $request->full_name;
        $new->email             = $request->email;
        $bdate                  = Carbon::parse($request->bdate);
        $new->bdate             = $bdate->format('Y-m-d');
        $new->gender             = $request->gender;
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
        if($new->$do()) {
            $new->verify_link = route('verify_register', ['token' => $new->remember_token]);
            $new->retry_link = route('retry_register', ['token' => $new->remember_token]);
            $new->check_code = route('check_code', ['token' => $new->remember_token]);
            //Send Email
            Mail::to($new->email)->send(new UserRegister($new, 'Activate your account.', 'register'));
            return $this->success('Done Successfully, Please check your email.', [
                'verify_link' => $new->verify_link,
                'retry_link' => $new->retry_link,
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
        $reset_password = route('reset_password', ['token' => $user->remember_token]);
        $user->verify_link = route('verify_register', ['token' => $user->remember_token]);
        $user->check_code = route('check_code', ['token' => $user->remember_token]);
        //Send Email
        Mail::to($user->email)->send(new UserRegister($user, 'Activate your account.', 'register'));
        return $this->success('Done Successfully, Please check your email.', [
            'reset_password' => $reset_password,
            'verify_link' => $user->verify_link,
            'check_code' => $user->check_code,
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
        $reset_password = route('reset_password', ['token' => $user->remember_token]);
        $check_code = route('check_code', ['token' => $user->remember_token]);
        $retry_link = route('retry_register', ['token' => $user->remember_token]);
        //Send Email
        Mail::to($user->email)->send(new UserRegister($user, 'Password Reset Request.', 'forget'));
        return $this->success('Please check your email to reset the password.', [
            'reset_password' => $reset_password,
            'check_code' => $check_code,
            'retry_link' => $retry_link,
        ]);
    }

    protected function checkCode(Request $request, $token) {
        $validator = Validator::make($request->all(), [
            'code' => 'required|size:5',
        ]);
        if($validator->fails()){
            return $this->failure('Invalid given code.');
        }
        $user = User::where(['remember_token' => $token, 'vtoken' => $request->code])->first();
        if(!empty($user)){
            return $this->success('token is valid.');
        }
        return $this->failure('Invalid given code.');
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
