<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class IncHdController extends Controller
{
    public function index()
    {

        $amount_qc_users = DB::connection('mysql_main_qc')
            ->table('qc_log_data as ld')
            ->select('qu.emp_name', 'ld.empqc',
                DB::raw('COUNT(ld.empqc) as empqc_count'),
                DB::raw('DATE_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(p.timeperpcs))), "%H:%i") as HM'),
                DB::raw('DATE_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(p.timeperpcs)) / 22), "%H:%i") as HD'),
                DB::raw('SUM(CASE WHEN lv.levelname = "Very easy" THEN 1 ELSE 0 END) AS level_very_easy'),
                DB::raw('SUM(CASE WHEN lv.levelname = "Easy" THEN 1 ELSE 0 END) AS level_easy'),
                DB::raw('SUM(CASE WHEN lv.levelname = "Middling" THEN 1 ELSE 0 END) AS level_middling'),
                DB::raw('SUM(CASE WHEN lv.levelname = "Hard" THEN 1 ELSE 0 END) AS level_hard'),
                DB::raw('SUM(CASE WHEN lv.levelname = "Very Hard" THEN 1 ELSE 0 END) AS level_very_hard'),
                DB::raw('CASE
            WHEN SEC_TO_TIME(SUM(TIME_TO_SEC(p.timeperpcs)) / 22) >= "07:00" AND SEC_TO_TIME(SUM(TIME_TO_SEC(p.timeperpcs)) / 22) < "07:31" THEN "C"
            WHEN SEC_TO_TIME(SUM(TIME_TO_SEC(p.timeperpcs)) / 22) >= "07:31" AND SEC_TO_TIME(SUM(TIME_TO_SEC(p.timeperpcs)) / 22) < "08:00" THEN "B"
            WHEN SEC_TO_TIME(SUM(TIME_TO_SEC(p.timeperpcs)) / 22) >= "08:00" AND SEC_TO_TIME(SUM(TIME_TO_SEC(p.timeperpcs)) / 22) < "09:00" THEN "A"
            WHEN SEC_TO_TIME(SUM(TIME_TO_SEC(p.timeperpcs)) / 22) >= "09:00" THEN "A+"
            ELSE "ไม่ผ่าน"
        END AS grade'),


            )
            ->leftJoin('qc_prod as p', 'ld.skucode', '=', 'p.pid')
            ->leftJoin('qc_level as lv', 'p.levelid', '=', 'lv.levelid')
            ->leftJoin('qc_user as qu', 'ld.empqc', '=', 'qu.emp_no')
            ->whereBetween('datekey', ['2024-04-01', DB::raw('LAST_DAY("2024-04-01")')])
            ->groupBy('qu.emp_name', 'ld.empqc')
            ->get();


        foreach ($amount_qc_users as $user) {
            dd($user->grade);
        }
        return response()->json($amount_qc_users);


    }

}

//$amount_qc_users = DB::connection('mysql_main_qc')
//    ->table('qc_log_data as ld')
//    ->select('qu.emp_name', 'ld.empqc',
//        DB::raw('COUNT(ld.empqc) as empqc_count'),
//        DB::raw('DATE_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(p.timeperpcs))), "%H:%i") as HM'),
//        DB::raw('DATE_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(p.timeperpcs)) / 22), "%H:%i") as HD'),
//        DB::raw('SUM(CASE WHEN lv.levelname = "Very easy" THEN 1 ELSE 0 END) AS level_very_easy'),
//        DB::raw('SUM(CASE WHEN lv.levelname = "Easy" THEN 1 ELSE 0 END) AS level_easy'),
//        DB::raw('SUM(CASE WHEN lv.levelname = "Middling" THEN 1 ELSE 0 END) AS level_middling'),
//        DB::raw('SUM(CASE WHEN lv.levelname = "Hard" THEN 1 ELSE 0 END) AS level_hard'),
//        DB::raw('SUM(CASE WHEN lv.levelname = "Very Hard" THEN 1 ELSE 0 END) AS level_very_hard'),
//        DB::raw('CASE
//            WHEN SEC_TO_TIME(SUM(TIME_TO_SEC(p.timeperpcs)) / 22) >= "07:00" AND SEC_TO_TIME(SUM(TIME_TO_SEC(p.timeperpcs)) / 22) < "07:31" THEN "C"
//            WHEN SEC_TO_TIME(SUM(TIME_TO_SEC(p.timeperpcs)) / 22) >= "07:31" AND SEC_TO_TIME(SUM(TIME_TO_SEC(p.timeperpcs)) / 22) < "08:00" THEN "B"
//            WHEN SEC_TO_TIME(SUM(TIME_TO_SEC(p.timeperpcs)) / 22) >= "08:00" AND SEC_TO_TIME(SUM(TIME_TO_SEC(p.timeperpcs)) / 22) < "09:00" THEN "A"
//            WHEN SEC_TO_TIME(SUM(TIME_TO_SEC(p.timeperpcs)) / 22) >= "09:00" THEN "A+"
//            ELSE "ไม่ผ่าน"
//        END AS grade')
//    )
//    ->leftJoin('qc_prod as p', 'ld.skucode', '=', 'p.pid')
//    ->leftJoin('qc_level as lv', 'p.levelid', '=', 'lv.levelid')
//    ->leftJoin('qc_user as qu', 'ld.empqc', '=', 'qu.emp_no')
//    ->whereBetween('datekey', ['2024-04-01', DB::raw('LAST_DAY("2024-04-01")')])
//    ->groupBy('qu.emp_name', 'ld.empqc')
//    ->get();
//
//return response()->json($amount_qc_users);
