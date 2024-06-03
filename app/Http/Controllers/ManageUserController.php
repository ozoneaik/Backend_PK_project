<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignupRequest;
use App\Models\User;
use Illuminate\Http\Request;

class ManageUserController extends Controller
{
    //
    public function userList()
    {
        $users = User::all();
        return response()->json([
            'users' => $users,
            'msg' => $users->isNotEmpty() ? 'ดึงข้อมูลสำเร็จ' : 'ดึงข้อมูลไม่สำเร็จ'
        ], $users->isNotEmpty() ? 200 : 400);
    }


    public function userStore(SignupRequest $request)
    {
        $user = new User();
        $user->email = $request->email;
        $user->name = $request->name;
        $user->password = bcrypt($request->password);
        $user->emp_role = $request->emp_role;
        $user->authcode = $request->authcode;
        $user->emp_status = $request->emp_status;
        $user->incentive = $request->incentive;
        $user->save();
        return response()->json(['user' => $user, 'msg' => 'สร้างผู้ใช้สำเร็จ!'], 200);
    }

    public function userUpdate(SignupRequest $request,$id){
        $user = User::find($id);
        if (!$user) {
            return response()->json(['msg' => 'ไม่พบผู้ใช้ที่ระบุ'], 404);
        }
        $user->email = $request->email;
        $user->name = $request->name;
        $user->password = bcrypt($request->password);
        $user->emp_role = $request->emp_role;
        $user->authcode = $request->authcode;
        $user->emp_status = $request->emp_status;
        $user->incentive = $request->incentive;
        $user->save();
        return response()->json(['user' => $user, 'msg' => 'สร้างผู้ใช้สำเร็จ!'], 200);
    }

    public function userDelete($id){
        $user = User::find($id);
        if (!$user) {
            return response()->json(['msg' => 'ไม่พบผู้ใช้ที่ระบุ'], 404);
        }
        $user->delete();
        return response()->json(['msg' => 'ลบผู้ใช้สำเร็จ!'], 200);
    }
}
