<?php

namespace App\Http\Controllers;

use App\Models\inc_hd;
use Illuminate\Support\Facades\DB;

class GetQcYear extends Controller
{
    //ดึงจำนวนงาน QC ในปีนั้นๆแต่ละเดือน
    public function getQcYear($year){
        $currentMonth = date('n'); // เดือนปัจจุบัน (1-12)

        $getIncHds = inc_hd::where('yearkey', $year)->orderBy('monthkey', 'asc')->get();

        $allMonths = [];
        for ($month = 1; $month <= $currentMonth; $month++) {
            $monthData = $getIncHds->firstWhere('monthkey', $month);
            if ($monthData) {
                $allMonths[] = $monthData;
            } else {
                $allMonths[] = [
                    'yearkey' => $year,
                    'monthkey' => $month,
                    'paydate' => null,
                    'workday' => null,
                    'status' => '-',
                    'numofemp' => null,
                    'totalqcqty' => null,
                    'totaltimepermonth' => null,
                    'totaltimeperday' => null,
                    'gradeteam' => null,
                    'payamntteam' => null,
                    'createbycode' => null,
                    'updatebycode' => null,
                    'caldate' => null,
                    'confirmdate' => null,
                    'confirmapprove' => null,
                    'confirmapprovebycode' => null,
                    'confirmpaydatebycode' => null,
                    'confirmpaydate' => null,
                    'created_at' => null,
                    'updated_at' => null
                ];
            }
        }

        return response()->json([
            'message' => 'ดึงข้อมูลสำเร็จ',
            'list' => $allMonths,
        ]);
    }
}
