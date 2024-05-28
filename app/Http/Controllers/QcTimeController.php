<?php

namespace App\Http\Controllers;

use App\Models\qc_time;
use Illuminate\Http\Request;

class QcTimeController extends Controller
{
    //
    public function index(){
        $times = qc_time::all();
        if ($times){
            return response()->json(['times' => $times, 'msg' => 'ดึงข้อมูลสำเร็จ'],200);
        }else{
            return response()->json(['times' => null, 'msg' => 'ดึงข้อมูลไม่สำเร็จข้อมูลอาจว่างเปล่า'],400);
        }
    }
}
