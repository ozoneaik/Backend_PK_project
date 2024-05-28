<?php

namespace App\Http\Controllers;

use App\Models\inc_dt;
use Carbon\Carbon;
use Illuminate\Http\Request;

class IncDtController extends Controller
{
    //


    public function store($datas, $IncHdId)
    {
        foreach ($datas as $key => $data) {
            for ($i = 1; $i <= 5; $i++) {
                $IncDt = new inc_dt();
                $IncDt->inc_id = $IncHdId;
                $IncDt->empqccode = $data['empqc'];
                $IncDt->qcqty = $data['empqc_count'];
                $IncDt->timepermonth = $data['HM'];
                $IncDt->timeperday = $data['HD'];
                $IncDt->grade = $data['grade'];
                switch ($i) {
                    case 1:
                        $le_name = 'Very Easy';
                        $rate = $data['rateVeryEasy'];
                        break;
                    case 2:
                        $le_name = 'Easy';
                        $rate = $data['rateEasy'];
                        break;
                    case 3:
                        $le_name = 'Middling';
                        $rate = $data['rateMiddling'];
                        break;
                    case 4:
                        $le_name = 'Hard';
                        $rate = $data['rateHard'];
                        break;
                    case 5:
                        $le_name = 'Very Hard';
                        $rate = $data['rateVeryHard'];
                        break;
                    default:
                        $le_name = '';
                        $rate = 0;
                }

                $IncDt->le_id = $i;
                $IncDt->le_name = $le_name;
                $IncDt->rate = $rate;

                $IncDt->payamnt = $data['total_received'];
                $IncDt->paystatus = $data['status'];
                $IncDt->payremark = 'จ่าย ไม่เจ่าย';
                $IncDt->createbycode = auth()->user()->authcode;
                $IncDt->created_at = Carbon::now();
                $IncDt->updatebycode = auth()->user()->authcode;
                $IncDt->updated_at = Carbon::now();
                $IncDt->save();
            }

        }

        return true;
    }
}
