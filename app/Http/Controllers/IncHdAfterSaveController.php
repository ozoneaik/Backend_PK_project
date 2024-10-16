<?php

namespace App\Http\Controllers;

use App\Models\inc_detail;
use App\Models\inc_dt;
use App\Models\inc_hd;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IncHdAfterSaveController extends Controller
{
    //
    public function getDataLocal($year, $month, $status) : JsonResponse
    {
        $old_data_teams = inc_hd::where('yearkey', $year)->where('monthkey', $month)->first();
        $QC_name = User::where('authcode', $old_data_teams->updatebycode)->first();
        if (!$QC_name) {
            $QC_name = '';
        }else{
            $QC_name = "( $QC_name->authcode ) $QC_name->name";
        }
        $IncDts = DB::table('inc_dts')
            ->select(
                'empqccode AS empqc',
                DB::raw('MAX(grade) AS Grade'),
                DB::raw("MAX(CASE WHEN le_name = 'Very Easy' THEN rate ELSE NULL END) AS RateVeryEasy"),
                DB::raw("MAX(CASE WHEN le_name = 'Easy' THEN rate ELSE NULL END) AS RateEasy"),
                DB::raw("MAX(CASE WHEN le_name = 'Middling' THEN rate ELSE NULL END) AS RateMiddling"),
                DB::raw("MAX(CASE WHEN le_name = 'Hard' THEN rate ELSE NULL END) AS RateHard"),
                DB::raw("MAX(CASE WHEN le_name = 'Very Hard' THEN rate ELSE NULL END) AS RateVeryHard"),
                'paystatus',
                'payremark',
                'payamnt',
                'qcqty AS EmpqcCount',
                'timepermonth AS HM',
                'timeperday AS HD'
            )
            ->where('inc_id', $old_data_teams->id)
            ->groupBy(
                'empqccode',
                'paystatus',
                'payremark',
                'payamnt',
                'qcqty',
                'timepermonth',
                'timeperday'
            )
            ->orderBy('empqc', 'asc')
            ->get();

        Log::info($IncDts);

        $amount_qc_users = [];
        $totalVeryEasy = 0;
        $totalEasy = 0;
        $totalMiddling = 0;
        $totalHard = 0;
        $totalVeryHard = 0;
        $total_person_received_teams = 0;
        $total_received_team = match ($old_data_teams->gradeteam) {
            'A', 'A+' => 200,
            'B' => 150,
            'C' => 100,
            default => 0,
        };

        foreach ($IncDts as $index=>$IncDt) {
//            $levels = DB::table('inc_details')
//                ->select(
//                    DB::raw('SUM(CASE WHEN le_id = 1 THEN skuqty ELSE 0 END) AS level_very_easy'),
//                    DB::raw('SUM(CASE WHEN le_id = 2 THEN skuqty ELSE 0 END) AS level_easy'),
//                    DB::raw('SUM(CASE WHEN le_id = 3 THEN skuqty ELSE 0 END) AS level_middling'),
//                    DB::raw('SUM(CASE WHEN le_id = 4 THEN skuqty ELSE 0 END) AS level_hard'),
//                    DB::raw('SUM(CASE WHEN le_id = 5 THEN skuqty ELSE 0 END) AS level_very_hard')
//                )
//                ->where('empqccode', $IncDt->empqc)
//                ->first();
            $levels = DB::table('inc_details')
                ->select(DB::raw('SUM(skuqty) as total_skuqty'), 'le_name')
                ->where('empqccode', $IncDt->empqc)
                ->where('inc_id', $old_data_teams->id)
                ->where('le_name' ,'!=','No QC')
                ->groupBy('le_name')
                ->get();
//            dd($levels,$IncDt->empqc);
            $empqc_name = DB::connection('mysql_main_qc')->table('qc_user')->select('emp_name')->where('emp_no', $IncDt->empqc)->first();
            foreach ($levels as $level) {
                switch ($level->le_name) {
                    case 'Very easy':
                        $level_very_easy = $level->total_skuqty;
                        break;
                    case 'Easy':
                        $level_easy = $level->total_skuqty;
                        break;
                    case 'Middling':
                        $level_middling = $level->total_skuqty;
                        break;
                    case 'Hard':
                        $level_hard = $level->total_skuqty;
                        break;
                    case 'Very Hard':
                        $level_very_hard = $level->total_skuqty;
                        break;
                }
            }


            $amount_qc_users[] = [
                'HM' => $IncDt->HM,
                'HD' => $IncDt->HD,
                'emp_name' => $empqc_name->emp_name,
                'empqc' => $IncDt->empqc,
                'empqc_count' => $IncDt->EmpqcCount,
                'grade' => $IncDt->grade,
                'level_very_easy' => $level_very_easy ?? 0,
                'level_easy' => $level_easy ?? 0,
                'level_middling' => $level_middling ?? 0,
                'level_hard' => $level_hard ?? 0,
                'level_very_hard' => $level_very_hard ?? 0,
                'rateVeryEasy' => $IncDt->rateveryeasy,
                'rateEasy' => $IncDt->rateeasy,
                'rateMiddling' => $IncDt->ratemiddling,
                'rateHard' => $IncDt->ratehard,
                'rateVeryHard' => $IncDt->rateveryhard,
                'paystatus' => $IncDt->paystatus,
                'total_person_received' => $IncDt->payamnt-$total_received_team,
                'total_received' => $IncDt->payamnt,
                'payremark' => $IncDt->payremark,
            ];
//            dd($amount_qc_users[$index]['total_person_received']);

            $totalVeryEasy += $level_very_easy;
            $totalEasy += $level_easy;
            $totalMiddling += $level_middling;
            $totalHard += $level_hard;
            $totalVeryHard += $level_very_hard;

            $total_person_received = floor(
                ($level_very_easy * $IncDt->rateveryeasy) +
                ($level_easy * $IncDt->rateeasy) +
                ($level_middling * $IncDt->ratemiddling) +
                ($level_hard * $IncDt->ratehard) +
                ($level_very_hard * $IncDt->rateveryhard)
            );
//            $total_person_received_teams += $total_person_received;
            $total_person_received_teams += $amount_qc_users[$index]['total_person_received'];
        }





        $data_teams = [
            'status' => $old_data_teams->status,
            'average_grade' => $old_data_teams->gradeteam,
            'average_time_HD' => $old_data_teams->totaltimeperday,
            'average_time_HM' => $old_data_teams->totaltimepermonth,

            'totalVeryEasy' => strval($totalVeryEasy),
            'totalEasy' => strval($totalEasy),
            'totalMiddling' => strval($totalMiddling),
            'totalHard' => strval($totalHard),
            'totalVeryHard' => strval($totalVeryHard),

            'totalPersonReceived' => $total_person_received_teams,
            'total_empqc_teams' => $old_data_teams->totalqcqty,
            'total_received_team' => $total_received_team,

            'workday' => $old_data_teams->workday,

            'total_receiveds' => doubleval($old_data_teams->payamntteam),

            'createbycode' => $old_data_teams->createbycode,
            'updatebycode' => $QC_name,

            'confirmapprovebycode' => $old_data_teams->confirmapprovebycode,
            'confirmpaydatebycode' => $old_data_teams->confirmpaydatebycode,
        ];


        if ($amount_qc_users) {
            return response()->json([
                'amount_qc_users' => $amount_qc_users,
                'data_teams' => $data_teams,
                'inc_id' => $old_data_teams->id,
                'msg' => 'heloworld'
            ], 200);
        } else {
            return response()->json(['amount_qc_users' => null, 'data_teams' => null,'msg' => 'เกิดข้อผิดพลาด (Inc_dt or Detail not found)'], 400);
        }
    }
}
