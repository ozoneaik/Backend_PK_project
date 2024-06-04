<?php

namespace App\Http\Controllers;

use App\Models\qc_time;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QcTimeController extends Controller
{
    //
    public function index(){
        $times = qc_time::orderBy('id','asc')->get();
        if ($times){
            return response()->json(['times' => $times, 'msg' => 'ดึงข้อมูลสำเร็จ'],200);
        }else{
            return response()->json(['times' => null, 'msg' => 'ดึงข้อมูลไม่สำเร็จข้อมูลอาจว่างเปล่า'],400);
        }
    }

    public function update(Request $request){
        try {
            foreach ($request->updatedTimes as $index => $r){
                $findTime = qc_time::where('grade' , $r['grade'])->first(); // Use first() instead of get()
                if($findTime){
                    $findTime->time = $r['time'];
                    $findTime->save(); // Use save() instead of update()
                } else {
                    return response()->json(['msg' => 'Grade not found', 'code' => 404], 404);
                }
            }
            return response()->json(['msg' => 'บันทึกข้อมูลสำเร็จ'], 200);
        } catch (\Exception $e) {
            return response()->json(['msg' => $e->getMessage(), 'code' => $e->getCode()], 400);
        }
    }

}
