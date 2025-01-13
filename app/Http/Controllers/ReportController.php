<?php
namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function report(Request $request)
    {
        $request->validate(['datekey' => 'required',], ['datekey.required' => 'กรุณาเลือกเดือนที่ต้องการดูรายงาน']);
        $startTime = $request->datekey . '-01';
        $endTime = $request->datekey . '-31';
        $list = DB::connection('mysql_main_qc')->table('qc_log_data as ld')
            ->select(
                'ld.id',
                'ld.datekey',
                'ld.empqc',
                'qu.emp_name as empqc_name',
                'ld.serial',
                'ld.skucode',
                'p.pname',
                'p.timeperpcs',
                'lv.levelname',
                'ld.empkey',
                'qk.emp_name as empkey_name'
            )
            ->leftJoin('qc_prod as p', 'ld.skucode', '=', 'p.pid')
            ->leftJoin('qc_level as lv', 'p.levelid', '=', 'lv.levelid')
            ->leftJoin('qc_user as qu', 'ld.empqc', '=', 'qu.emp_no')
            ->leftJoin('qc_user as qk', 'ld.empkey', '=', 'qk.emp_no')
            ->whereBetween(DB::raw('DATE(ld.datekey)'), [$startTime, $endTime])->get();
        return response()->json([
            'list' => $list,
            'startTime' => $startTime,
            'endTime' => $endTime
        ]);
    }

    public function reportFromSystem (Request $request) : JsonResponse{
        $request->validate(['datekey' => 'required',], ['datekey.required' => 'กรุณาเลือกเดือนที่ต้องการดูรายงาน']);
        $startTime = $request->datekey . '-01';
        $endTime = $request->datekey . '-31';

        return response()->json([
            'list' => [],
            'startTime' => $startTime,
            'endTime' => $endTime
        ]);
    }
}
