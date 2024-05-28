<?php

namespace App\Http\Controllers;

use App\Models\inc_hd;
use App\Models\QcMain;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class IncHdController extends Controller
{
    public function qc_month($year, $month){
        $startOfMonth = "$year-$month-01";

        $amount_qc_users = QcMain::select('qc_user.emp_name', 'qc_log_data.empqc')
            ->selectRaw('COUNT(qc_log_data.empqc) as empqc_count')
            ->selectRaw('DATE_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(qc_prod.timeperpcs))), "%H:%i") as HM')
            ->selectRaw('DATE_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(qc_prod.timeperpcs)) / 22), "%H:%i") as HD')
            ->selectRaw('SUM(CASE WHEN qc_level.levelname = "Very easy" THEN 1 ELSE 0 END) AS level_very_easy')
            ->selectRaw('SUM(CASE WHEN qc_level.levelname = "Easy" THEN 1 ELSE 0 END) AS level_easy')
            ->selectRaw('SUM(CASE WHEN qc_level.levelname = "Middling" THEN 1 ELSE 0 END) AS level_middling')
            ->selectRaw('SUM(CASE WHEN qc_level.levelname = "Hard" THEN 1 ELSE 0 END) AS level_hard')
            ->selectRaw('SUM(CASE WHEN qc_level.levelname = "Very Hard" THEN 1 ELSE 0 END) AS level_very_hard')
            ->selectRaw('CASE
                WHEN SEC_TO_TIME(SUM(TIME_TO_SEC(qc_prod.timeperpcs)) / 22) >= "07:00" AND SEC_TO_TIME(SUM(TIME_TO_SEC(qc_prod.timeperpcs)) / 22) < "07:31" THEN "C"
                WHEN SEC_TO_TIME(SUM(TIME_TO_SEC(qc_prod.timeperpcs)) / 22) >= "07:31" AND SEC_TO_TIME(SUM(TIME_TO_SEC(qc_prod.timeperpcs)) / 22) < "08:00" THEN "B"
                WHEN SEC_TO_TIME(SUM(TIME_TO_SEC(qc_prod.timeperpcs)) / 22) >= "08:00" AND SEC_TO_TIME(SUM(TIME_TO_SEC(qc_prod.timeperpcs)) / 22) < "09:00" THEN "A"
                WHEN SEC_TO_TIME(SUM(TIME_TO_SEC(qc_prod.timeperpcs)) / 22) >= "09:00" THEN "A+"
                ELSE "ไม่ผ่าน"
            END AS grade')
            ->leftJoin('qc_prod', 'qc_log_data.skucode', '=', 'qc_prod.pid')
            ->leftJoin('qc_level', 'qc_prod.levelid', '=', 'qc_level.levelid')
            ->leftJoin('qc_user', 'qc_log_data.empqc', '=', 'qc_user.emp_no')
            ->where('qc_user.emp_name', 'NOT LIKE', 'QC%')
            ->where('qc_log_data.empqc', 'NOT LIKE', 'QC%')
            ->whereBetween('qc_log_data.datekey', [$startOfMonth, DB::raw("LAST_DAY('$startOfMonth')")])
            ->groupBy('qc_user.emp_name', 'qc_log_data.empqc')
            ->get();


        $total_empqc_teams = 0;


        $total_HD = 0; //HD
        $total_HM = 0; //HM
        $totalVeryEasy = 0; //totalVeryEasy
        $totalEasy = 0;
        $totalMiddling = 0;
        $totalHard = 0;
        $totalVeryHard = 0;
        $total_person_received_teams = 0;


        $total_users = count($amount_qc_users);

        $status = true;
        foreach ($amount_qc_users as $index => $user) {

            $user->rateVeryEasy = '0';
            $user->rateEasy = '0';
            $user->rateMiddling = '0';
            $user->rateHard = '0';
            $user->rateVeryHard = '0';
            if ($status) {
                switch ($user->grade) {
                    case 'A+':
                        $user->rateVeryEasy = '0.125';
                        $user->rateEasy = '0.1875';
                        $user->rateMiddling = '0.250';
                        $user->rateHard = '0.3125';
                        $user->rateVeryHard = '0.375';
                        break;
                    case 'A':
                        $user->rateVeryEasy = '0.113';
                        $user->rateEasy = '0.169';
                        $user->rateMiddling = '0.225';
                        $user->rateHard = '0.281';
                        $user->rateVeryHard = '0.338';
                        break;
                    case 'B':
                        $user->rateVeryEasy = '0.104';
                        $user->rateEasy = '0.156';
                        $user->rateMiddling = '0.208';
                        $user->rateHard = '0.260';
                        $user->rateVeryHard = '0.313';
                        break;
                    case 'C':
                        $user->rateVeryEasy = '0.083';
                        $user->rateEasy = '0.125';
                        $user->rateMiddling = '0.167';
                        $user->rateHard = '0.208';
                        $user->rateVeryHard = '0.250';
                        break;
                    default:
                        $user->rateVeryEasy = '0';
                        $user->rateEasy = '0';
                        $user->rateMiddling = '0';
                        $user->rateHard = '0';
                        $user->rateVeryHard = '0';
                        break;
                }
            }


            // หาผลรวมยอดรับบุคคลของแต่ละคน
            $user->total_person_received = round(
                ($user->level_easy * $user->rateEasy) +
                ($user->level_very_easy * $user->rateVeryEasy) +
                ($user->level_middling * $user->rateMiddling) +
                ($user->level_hard * $user->rateHard) +
                ($user->level_very_hard * $user->rateVeryHard), 2
            );


            //Teams
            // หาผลรวมปริมาณ QC รวมทั้งหมด
            $total_empqc_teams = $total_empqc_teams + $user->empqc_count;
            // หาเวลาเฉลี่ยทั้งหมดในนาที ของ HD
            $total_HD += (intval(substr($user->HD, 0, 2)) * 60) + intval(substr($user->HD, 3));
            $average_minutes = $total_HD / $total_users;
            $average_hours = floor($average_minutes / 60);
            $average_minutes = $average_minutes % 60;
            $average_time_HD = sprintf('%02d:%02d:00', $average_hours, $average_minutes);
            // หาเวลาเฉลี่ยทั้งหมดในนาที ของ HM
            $total_HM += (intval(substr($user->HM, 0, 4)) * 60) + intval(substr($user->HM, 4));
            $average_hours = floor($total_HM / 60);
            $average_minutes = $average_minutes % 60;
            $average_time_HM = sprintf('%02d:%02d:00', $average_hours, $average_minutes);
            // ตัดเกรดตามเวลาเฉลี่ย
            if ($average_time_HD >= '07:00:00' && $average_time_HD < '07:31:00') {
                $average_grade = 'C';
            } else if ($average_time_HD >= '07:31:00' && $average_time_HD < '08:00:00') {
                $average_grade = 'B';
            } else if ($average_time_HD >= '08:00:00' && $average_time_HD < '09:00:00') {
                $average_grade = 'A';
            } else if ($average_time_HD >= '09:00:00') {
                $average_grade = 'A+';
            } else {
                $average_grade = 'ไม่ผ่าน';
            }

            //เก็บปริมาณรวมในแต่ละ level
            $totalVeryEasy += $user->level_very_easy;
            $totalEasy += $user->level_easy;
            $totalMiddling += $user->level_middling;
            $totalHard += $user->level_hard;
            $totalVeryHard += $user->level_very_hard;

            $total_person_received_teams += $user->total_person_received;

        }// end for

        //เก็บยอดรับทีม
        if ($average_grade == 'A+' || $average_grade == 'A') {
            $total_received_team = 200;
        } else if ($average_grade == 'B') {
            $total_received_team = 150;
        } else if ($average_grade == 'C') {
            $total_received_team = 100;
        } else {
            $total_received_team = 0;
        }

        //หาผลรวมยอดรับสุทธิของแต่ละคน
        $total_receiveds = 0;
        foreach ($amount_qc_users as $index => $user) {
            $user->total_received = $user->total_person_received + $total_received_team;
            if ($index == 1) {
//                dd($user->total_received);
            }
            $total_receiveds += $user->total_received;
        }


        $data_teams = [
            'average_time_HD' => $average_time_HD,
            'average_time_HM' => $average_time_HM,
            'average_grade' => $average_grade,

            'totalVeryEasy' => $totalVeryEasy,
            'totalEasy' => $totalEasy,
            'totalMiddling' => $totalMiddling,
            'totalHard' => $totalHard,
            'totalVeryHard' => $totalVeryHard,

            'totalPersonReceived' => $total_person_received_teams,
            'total_received_team' => $total_received_team,
            'total_receiveds' => $total_receiveds,

            'total_empqc_teams' => $total_empqc_teams,
        ];
//        dd($data_teams);
//        dd($average_time_HD, $average_grade, $total_empqc_teams, $average_time_HM, $totalVeryEasy / count($amount_qc_users));

        if ($amount_qc_users){
            return response()->json(['amount_qc_users' => $amount_qc_users, 'data_teams' => $data_teams,],200);
        }else{
            return response()->json(['amount_qc_users' => null, 'data_teams' => null,],400);
        }


    }



    public function store(Request $request){
        $datas = $request->datas;
        $data_team = $request->NewData_team;

        // ตรวจสอบว่ามี year และ month ที่ต้องการสร้างหรือไม่
        $existingIncHd = inc_hd::where('yearkey', $data_team['year'])->where('monthkey', $data_team['month'])->first();
        if($existingIncHd) {return response()->json(['msg' => 'มีข้อมูลสำหรับเดือน '.$data_team['month'].  '/' .$data_team['year']. ' นี้อยู่แล้ว'], 400);}

        //Insert INTO inc_hds ( ทีม )
        $IncHd = new inc_hd();
        $IncHd->monthkey = $data_team['month'];
        $IncHd->yearkey = $data_team['year'];
        $IncHd->paydate = $data_team['month'] + 1;
        $IncHd->workday = 22;
        $IncHd->status = $data_team['status'];
        $IncHd->numofemp = count($datas);
        $IncHd->totalqcqty = $data_team['total_empqc_teams'];
        $IncHd->totaltimepermonth = $data_team['average_time_HM']; // กำหนดค่าให้กับ IncHd
        $IncHd->totaltimeperday = $data_team['average_time_HD'];
        $IncHd->gradeteam = $data_team['average_grade'];
        $IncHd->payamntteam = $data_team['total_receiveds'];
        $IncHd->created_at = Carbon::now();
        $IncHd->createbycode = auth()->user()->authcode;
        $IncHd->updated_at = Carbon::now();
        $IncHd->updatebycode = auth()->user()->authcode;


        if ($IncHd->save()){
            $InsertIncDt = App::make('App\Http\Controllers\IncDtController')->store($datas,$IncHd->id);
            if ($InsertIncDt){
                return  response()->json(['msg' => 'สร้างข้อมูลสำเร็จ'],200);
//                $InsertIncDetail = App::make('App\Http\Controllers\IncDetailController')->store($IncHd->id);
            }else{

            }

            return  response()->json(['msg' => 'สร้างข้อมูลสำเร็จ'],200);
        }else{
            return response()->json(['msg' => 'สร้างข้อมูลไม่สำเร็จ'],400);
        }

    }

}


