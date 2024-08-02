<?php

namespace App\Http\Controllers;

use App\Models\qc_workday;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QcWorkdayController extends Controller
{
    //

    public function index(){
        $workdays = qc_workday::all();
        return response()->json([
            'workdays' => $workdays,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $workday = $request->all();
        $year_month = $workday['wo_year'] . '-' . $workday['wo_month'];
        $workdays = qc_workday::all();
        foreach ($workdays as $check) {
            $checkBase = $check->wo_year . '-' . $check->wo_month;
            if ($year_month == $checkBase) {
                return response()->json([
                    'message' => 'ขออภัยคุณเพิ่มจำนวนวันของ '.$year_month. ' แล้ว'
                ],400);
            }
        }

        $insert = new qc_workday();
        $insert->wo_year = $workday['wo_year'];
        $insert->wo_month = $workday['wo_month'];
        $insert->workday = $workday['workday'];
        $insert->save();
        return response()->json([
            'message' => 'บันทึกข้อมูลเสร็จสิ้น'
        ],200);
    }

    public function update(Request $request){
        try {
            $data = $request->data;
            foreach ($data as $key => $value) {
                $update = qc_workday::find($value['id']);
                $update->wo_year = $value['wo_year'];
                $update->wo_month = $value['wo_month'];
                $update->workday = $value['workday'];
                $update->save();
            }

            return response()->json([
                'message' => 'อัพเดทข้อมูลสำเร็จ'
            ]);
        }catch (\Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ]);
        }
    }
}
