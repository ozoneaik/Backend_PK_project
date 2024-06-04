<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\SignupRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller{


    public function signup(SignupRequest $request)
    {
        $data = $request->validated();

        /** @var \App\Models\User $user */
        $user = new User();
        $user->emp_role = 'HR';
        $user->authcode = $data['name'];
        $user->name = $data['name'];
        $user->emp_status = 'active';
        $user->incentive = 'incentive';
        $user->email = $data['email'];
        $user->password = bcrypt($data['password']);
        $user->save();
        $token = $user->createToken('main')->plainTextToken;

        if ($user){
            if ($token){
                return response()->json(['msg' => 'สร้าง user สำเร็จ'],200);
            }
        }else{
            return response()->json(['msg'=>'สร้าง user ไม่สำเร็จ'],400);
        }
    }



    public function login(LoginRequest $request){
        $credentials = $request->validated();
        $remember = $credentials['remember'] ?? false;
        unset($credentials['remember']);
        // ตรวจสอบข้อมูลผู้ใช้จากฐานข้อมูลโดยใช้ Eloquent หรือ Query Builder
        $user = User::where('email', $credentials['email'])->first();
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response([
                'error' => 'The Provided credentials are not correct'
            ], 422);
        }
//      $user = Auth::user();
        $token = $user->createToken('main')->plainTextToken;
        return response([
            'user' => $user,
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        // Revoke the token that was used to authenticate the current request...
        $user->currentAccessToken()->delete();

        return response([
            'success' => true
        ]);
    }

    public function me(Request $request)
    {
        return $request->user();
    }
}
