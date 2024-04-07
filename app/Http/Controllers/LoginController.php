<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required',
            'password' => 'required'
        ]);

        if($validator->fails()){
            return $this->failure('Mobile key is required');
        }
        $check = User::where('mobile', $request->mobile)->where('active', true)->first();
        if (empty($check)) {
            return $this->failure('Invalid Mobile or Password.');
        }
        $user = Auth::loginUsingId($user->id);
        $token = $user->createToken($user->mobile)->plainTextToken;
        return $this->success('Successful Login', [
            'token' => $token
        ]);

    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();
        return $this->success('Successfully logout.');
    }
}
