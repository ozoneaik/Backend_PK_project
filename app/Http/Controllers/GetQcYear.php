<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GetQcYear extends Controller
{
    //ดึงจำนวนงาน QC ในปีนั้นๆแต่ละเดือน
    public function getQcYear($year){

        $results = DB::connection('mysql_main_qc')->table('qc_log_data')
            ->select(
                DB::raw("DATE_FORMAT(datekey, '%Y-%m') AS year"),
                DB::raw('COUNT(job_id) as job_count'),
                DB::raw('COUNT(DISTINCT empqc) AS user_count'),
                DB::raw('COUNT(DISTINCT DATE_FORMAT(datekey, "%Y-%m-%d")) AS day')
            )
            ->where('datekey', 'LIKE', "$year-%-%")
            ->groupBy(DB::raw("DATE_FORMAT(datekey, '%Y-%m')"))
            ->get();


        if (count($results) > 0){
            return response()->json(['results' => $results,'msg' => 'ตรวจพบรายสินค้าประจำปี '.$year], 200);

        }else{
            return response()->json(['results' => null,'msg' => 'ไม่พบรายการสินค้าประจำปี '.$year],400);
        }
    }
}
