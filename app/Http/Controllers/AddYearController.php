<?php

namespace App\Http\Controllers;

use App\Models\addYear;
use Illuminate\Http\Request;


class AddYearController extends Controller
{
    //
    public function addYear(Request $request){
        $request->validate([
            'year' => 'required|numeric|digits:4|unique:add_years,year',
        ],[
            'yaer.required' => 'กรุณากรอกปี',
            'year.unique' => 'คุณได้เพิ่มปี'.$request->year.'ไปแล้ว ไม่สามารถเพิ่มซ้ำได้'
        ]);

        $addYear = new addYear();
        $addYear->year = $request->input('year');
        $addYear->save();
        if ($addYear->save()){
            return response()->json(['msg' => 'บันทึกข้อมูลเสร็จสิ้น'],200);
        }else{
            return response()->json(['msg' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล'], 500);
        }
    }

    public function ListYear(){
        $listYear = AddYear::orderBy('year', 'desc')->pluck('year')->unique();
        return response()->json($listYear,200);
    }
}
