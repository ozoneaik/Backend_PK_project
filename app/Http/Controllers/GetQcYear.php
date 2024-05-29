<?php

namespace App\Http\Controllers;

use App\Models\inc_hd;
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


        $getIncHds = inc_hd::where('yearkey', $year)->get();
        if (count($getIncHds) > 0) {
            foreach ($results as $result) {
                $matched = false;
                foreach ($getIncHds as $getIncHd) {
                    $y = sprintf("%d-%02d", $getIncHd->yearkey, $getIncHd->monthkey);
                    if ($result->year == $y) {
                        $result->status = $getIncHd->status;
                        $result->user_count = $getIncHd->numofemp;
                        $result->job_count = $getIncHd->totalqcqty;
                        $result->updated_at = $getIncHd->updated_at;
                        $matched = true;
                        break;
                    }
                }
                if (!$matched) {
                    $result->status = '-';
                    $result->updated_at = null;
                }
            }
        } else {
            foreach ($results as $result) {
                $result->status = '-';
                $result->updated_at = null;
            }
        }

        if (count($results) > 0){
            return response()->json(['results' => $results,'msg' => 'ตรวจพบรายสินค้าประจำปี '.$year], 200);

        }else{
            return response()->json(['results' => null,'msg' => 'ไม่พบรายการสินค้าประจำปี '.$year],400);
        }
    }
}
