<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ManageUserController extends Controller
{
    //
    public function userList(){
        $users = User::all();
        if ($users) {
            return response()->json([
                'users' => $users,
                'msg' => 'ดึงข้อมูลสำเร็จ'
            ],200);
        }else{
            return response()->json([
                'users' => null,
                'msg' => 'ดึงข้อมูลไม่สำเร็จ'
            ],400);
        }
    }
}
