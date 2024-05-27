<?php

namespace App\Http\Controllers;

use App\Models\qc_workday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QcWorkdayController extends Controller
{
    //

    public function index(){
        $workdays = qc_workday::orderByRaw("CONCAT(wo_year, '/', wo_month) DESC")->get();
        if ($workdays){
            return response()->json([
                'workdays' => $workdays,
            ],200);
        }else{
            return response()->json([],400);
        }
    }

    public function getYears(){
        $workdays = qc_workday::select('wo_year')->distinct()->get();
        return response()->json(
            $workdays
        );
    }


    public function store(Request $request)
    {
        $request->validate([
            'wo_year' => ['required'],
            'wo_month' => ['required'],
            'workday' => ['required'],
        ],[
            'wo_year.required' => 'กรุณากรอกปี',
            'wo_month.required' => 'กรุณากรอกเดือน',
            'workday.required' => 'กรุณากรอกวันทำงาน ( WorkDay )',
        ]);

        $results = DB::table('qc_workdays')
            ->where('wo_year', 'LIKE', $request->wo_year)
            ->where('wo_month', 'LIKE', $request->wo_month)
            ->get();

        if (!$results->isEmpty()){
            return response()->json([
                'status' => false,
                'message' => 'เคยกรอกข้อมูลนี้ไปแล้ว'
            ],400);
        }else{
            $workday = new qc_workday();
            $workday->wo_year = $request->input('wo_year');
            $workday->wo_month = $request->input('wo_month');
            $workday->workday = $request->input('workday');
            $workday->save();

            if ($workday){
                return response()->json([
                    'status' => true,
                    'message' => 'บันทึกข้อมูลสำเร็จ'
                ],200);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'บันทึกข้อมูลไม่สำเร็จ'
                ],400);
            }
        }
    }
}
