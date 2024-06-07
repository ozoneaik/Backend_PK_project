<?php

namespace App\Http\Controllers;
use App\Models\inc_detail;
use App\Models\inc_dt;
use App\Models\inc_hd;
use App\Models\qc_rate;
use App\Models\qc_time;
use App\Models\QcMain;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class IncHdController extends Controller
{

    //เช็คว่า มีข้อมูลนี้อยู่ในฐานข้อมูลหรือไม่ จากหน้าแก้ไข 😀
    public function checkIncHd($year, $month){
        $check = inc_hd::where('yearkey', $year)->where('monthkey', $month)->first();
        if ($check){
            return response()->json(['check' => $check,'msg' => 'ต้องการอัพเดทข้อมูลหรือไม่','inc_id'=>$check->id],200);
        }else{
            return response()->json(['check' => null,'msg' => 'ไม่เจอข้อมูล'],400);
        }
    }

    //ดึงข้อมูลจาก .30 แล้วคำนวณเพิ่อนำข้อมูลมาแสดง เตรียมส่งให้บันทึกไปยัง postgres😁
    public function qc_month($year, $month,$status){
        if ($status != '-'){
            return App::make('App\Http\Controllers\IncHdAfterSaveContoller')->getDataLocal($year, $month,$status);
        }
        $startOfMonth = "$year-$month-01";


        //ดึงข้อมูลนับจำนวนวันของเดือนนั้นๆ
        $monthPattern = str_pad($month, 2, '0', STR_PAD_LEFT);
        $datePattern = "{$year}-{$monthPattern}-%";
        $workdayQL = QcMain::where('datekey', 'LIKE', $datePattern)
            ->select(DB::raw('COUNT(DISTINCT DATE_FORMAT(datekey, "%Y-%m-%d")) AS day'))
            ->first();
        $workday = $workdayQL['day'];


        //ดึงข้อมูลจาก times
        $times = qc_time::orderBy('ti_id','asc')->get();
        $timeValues = $times->pluck('time')->toArray();

        $amount_qc_users = QcMain::select('qc_user.emp_name', 'qc_log_data.empqc')
            ->selectRaw('COUNT(qc_log_data.empqc) as empqc_count')
            ->selectRaw('DATE_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(qc_prod.timeperpcs))), "%H:%i") as HM')
            ->selectRaw('DATE_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(qc_prod.timeperpcs)) / '.$workday.'), "%H:%i") as HD')
            ->selectRaw('SUM(CASE WHEN qc_level.levelname = "Very easy" THEN 1 ELSE 0 END) AS level_very_easy')
            ->selectRaw('SUM(CASE WHEN qc_level.levelname = "Easy" THEN 1 ELSE 0 END) AS level_easy')
            ->selectRaw('SUM(CASE WHEN qc_level.levelname = "Middling" THEN 1 ELSE 0 END) AS level_middling')
            ->selectRaw('SUM(CASE WHEN qc_level.levelname = "Hard" THEN 1 ELSE 0 END) AS level_hard')
            ->selectRaw('SUM(CASE WHEN qc_level.levelname = "Very Hard" THEN 1 ELSE 0 END) AS level_very_hard')
            ->selectRaw('CASE
                WHEN SEC_TO_TIME(SUM(TIME_TO_SEC(qc_prod.timeperpcs)) / '.$workday.') >= "' . $timeValues[3] . '" AND SEC_TO_TIME(SUM(TIME_TO_SEC(qc_prod.timeperpcs)) / '.$workday.') < "' . $timeValues[2] . '" THEN "C"
                WHEN SEC_TO_TIME(SUM(TIME_TO_SEC(qc_prod.timeperpcs)) / '.$workday.') >= "' . $timeValues[2] . '" AND SEC_TO_TIME(SUM(TIME_TO_SEC(qc_prod.timeperpcs)) / '.$workday.') < "' . $timeValues[1] . '" THEN "B"
                WHEN SEC_TO_TIME(SUM(TIME_TO_SEC(qc_prod.timeperpcs)) / '.$workday.') >= "' . $timeValues[1] . '" AND SEC_TO_TIME(SUM(TIME_TO_SEC(qc_prod.timeperpcs)) / '.$workday.') < "' . $timeValues[0] . '" THEN "A"
                WHEN SEC_TO_TIME(SUM(TIME_TO_SEC(qc_prod.timeperpcs)) / '.$workday.') >= "' . $timeValues[0] . '" THEN "A+"
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
        $statuss = true;

        //ดึงข้อมูล rate
        $rate = qc_rate::orderBy('ra_id','asc')->get();
        foreach ($amount_qc_users as $index => $user) {
            $user->rateVeryEasy = '0';
            $user->rateEasy = '0';
            $user->rateMiddling = '0';
            $user->rateHard = '0';
            $user->rateVeryHard = '0';
            if ($statuss) {

                switch ($user->grade) {

                    case 'A+':
                        $user->rateVeryEasy = $rate[3]['rate'];
                        $user->rateEasy = $rate[7]['rate'];
                        $user->rateMiddling = $rate[11]['rate'];
                        $user->rateHard = $rate[15]['rate'];
                        $user->rateVeryHard = $rate[19]['rate'];
                        break;
                    case 'A':
                        $user->rateVeryEasy = $rate[2]['rate'];
                        $user->rateEasy = $rate[6]['rate'];
                        $user->rateMiddling = $rate[10]['rate'];
                        $user->rateHard = $rate[14]['rate'];
                        $user->rateVeryHard = $rate[18]['rate'];
                        break;
                    case 'B':
                        $user->rateVeryEasy = $rate[1]['rate'];
                        $user->rateEasy = $rate[5]['rate'];
                        $user->rateMiddling = $rate[9]['rate'];
                        $user->rateHard = $rate[13]['rate'];
                        $user->rateVeryHard = $rate[17]['rate'];
                        break;
                    case 'C':
                        $user->rateVeryEasy = $rate[0]['rate'];
                        $user->rateEasy = $rate[4]['rate'];
                        $user->rateMiddling = $rate[8]['rate'];
                        $user->rateHard = $rate[12]['rate'];
                        $user->rateVeryHard = $rate[16]['rate'];
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
            if ($average_time_HD >= $timeValues[3] && $average_time_HD < $timeValues[2]) {
                $average_grade = 'C';
            } else if ($average_time_HD >= $timeValues[2] && $average_time_HD < $timeValues[1]) {
                $average_grade = 'B';
            } else if ($average_time_HD >= $timeValues[1] && $average_time_HD < $timeValues[0]) {
                $average_grade = 'A';
            } else if ($average_time_HD >= $timeValues[0]) {
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

            'workday' => $workday,

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

        if ($amount_qc_users){
            return response()->json(['amount_qc_users' => $amount_qc_users, 'data_teams' => $data_teams,'msg' => 'ดึงข้อมูลสำเร็จ'],200);
        }else{
            return response()->json(['amount_qc_users' => null, 'data_teams' => null,'msg' => 'ดึงข้อมูลไม่สำเร็จ'],400);
        }
    }

    //สร้างข้อมูลลงใน ฐานข้อมูล😂
    public function store(Request $request){
        $datas = $request->datas;
        $data_team = $request->NewData_team;

        // ตรวจสอบว่ามี year และ month ที่ต้องการสร้างหรือไม่
        $existingIncHd = inc_hd::where('yearkey', $data_team['year'])->where('monthkey', $data_team['month'])->first();
        if($existingIncHd) {
            return response()->json(['msg' => 'มีข้อมูลสำหรับเดือน '.$data_team['month'].  '/' .$data_team['year']. ' นี้อยู่แล้ว'], 400);
        }

        // เริ่มการทำธุรกรรม
        DB::beginTransaction();

        try {

            $monthPattern = str_pad($data_team['month'], 2, '0', STR_PAD_LEFT);
            $datePattern = "{$data_team['year']}-{$monthPattern}-%";
            $workdayQL = QcMain::where('datekey', 'LIKE', $datePattern)
                ->select(DB::raw('COUNT(DISTINCT DATE_FORMAT(datekey, "%Y-%m-%d")) AS day'))
                ->first();
            $workday = $workdayQL['day'];

            // Insert INTO inc_hds (ทีม)
            $IncHd = new inc_hd();
            $IncHd->monthkey = $data_team['month'];
            $IncHd->yearkey = $data_team['year'];
            $IncHd->paydate = $data_team['month'] + 1;
            $IncHd->workday = $workday;
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
            $IncHd->caldate = Carbon::now();

            if ($IncHd->save()) {
                $InsertIncDt = App::make('App\Http\Controllers\IncDtController')->store($datas, $IncHd->id, $data_team['month'], $data_team['year']);
                if ($InsertIncDt) {
                    // Commit การทำธุรกรรมถ้าทุกอย่างสำเร็จ
                    DB::commit();
                    return response()->json(['msg' => 'สร้างข้อมูลสำเร็จ'], 200);
                } else {
                    // Rollback การทำธุรกรรมถ้ามีข้อผิดพลาด
                    DB::rollBack();
                    return response()->json(['msg' => 'สร้างข้อมูลไม่สำเร็จ'], 400);
                }
            } else {
                // Rollback การทำธุรกรรมถ้าการบันทึก $IncHd ไม่สำเร็จ
                DB::rollBack();
                return response()->json(['msg' => 'สร้างข้อมูลไม่สำเร็จ'], 400);
            }
        } catch (\Exception $e) {
            // Rollback การทำธุรกรรมถ้ามีข้อผิดพลาด
            DB::rollBack();
            return response()->json(['msg' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request){
//        dd($request->all());
        $IncHdId = $request->inc_id;

        // เช็คว่ามี ID หรือไม่
        if (!$IncHdId){
            return response()->json([
                'msg' => 'ไม่สามารถอัพเดทข้อมูลได้ กรุณากดที่เมนู "QC สินค้า ประจำปี" แล้วลองใหม่อีกครั้ง หรือติดต่อแผนก IT'
            ],400);
        }

        DB::beginTransaction();
        try {
            $updateIncHd = inc_hd::find($IncHdId);
            $updateIncHd->status = 'active';
            $updateIncHd->totalqcqty = $request->data_team['total_empqc_teams'];
            $updateIncHd->totaltimepermonth = $request->data_team['average_time_HM'];
            $updateIncHd->totaltimeperday = $request->data_team['average_time_HD'];
            $updateIncHd->gradeteam = $request->data_team['average_grade'];
            $updateIncHd->payamntteam = $request->data_team['total_receiveds'];
            $updateIncHd->updatebycode = auth()->user()->authcode;
            $updateIncHd->updated_at = Carbon::now();
            $updateIncHd->caldate = Carbon::now();
            $updateIncHd->confirmdate = null;
            $updateIncHd->confirmapprove = null;
            $updateIncHd->confirmpaydate = null;
            $updateIncHd->save();


            $RemoveIncDt = inc_dt::where('inc_id', $IncHdId)->get();
            foreach ($RemoveIncDt as $incDt) {
                $incDt->delete();
            }
            $RemoveInc_detail = inc_detail::where('inc_id', $IncHdId)->get();
            foreach ($RemoveInc_detail as $incDetail) {
                $incDetail->delete();
            }
            $InsertIncDt = App::make('App\Http\Controllers\IncDtController')->store($request->datas, $IncHdId, $updateIncHd->monthkey, $updateIncHd->yearkey);
            DB::commit();
            return response()->json([
                'msg' => 'อัพเดทข้อมูลสำเร็จ กดตกลงเพื่อดำเนินการต่อ'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'msg' => 'เกิดข้อผิดพลาดในการอัพเดทข้อมูล: ' . $e->getMessage()
            ], 500);
        }
    }




}


