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
            ->where('qu.emp_name', 'NOT like', 'QC%') // กรอง emp_name ที่มี QC นำหน้า
            ->where('ld.empqc', 'NOT like', 'QC%') // กรอง empqc ที่มี QC นำหน้า
            ->whereBetween('datekey', ['2024-04-01', DB::raw('LAST_DAY("2024-04-01")')])
            ->groupBy('qu.emp_name', 'ld.empqc')
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

        foreach ($amount_qc_users as $index=>$user) {
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
        if ($average_grade == 'A+' || $average_grade == 'A'){
            $total_received_team = 200;
        }else if ($average_grade == 'B'){
            $total_received_team = 150;
        }else if ($average_grade == 'C'){
            $total_received_team = 100;
        }else{
            $total_received_team = 0;
        }

        //หาผลรวมยอดรับสุทธิของแต่ละคน
        $total_receiveds = 0;
        foreach ($amount_qc_users as $index=>$user){
            $user->total_received = $user->total_person_received+$total_received_team;
            if ($index == 1){
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

        return response()->json(
            [
                'amount_qc_users' => $amount_qc_users,
                'data_teams' => $data_teams,
            ]
            );


    }

}


