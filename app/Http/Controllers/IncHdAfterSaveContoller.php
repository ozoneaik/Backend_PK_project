<?php

namespace App\Http\Controllers;

use App\Models\inc_dt;
use App\Models\inc_hd;
use Illuminate\Support\Facades\DB;

class IncHdAfterSaveContoller extends Controller
{
    //
    public function getDataLocal($year, $month,$status){
        $IncHds = inc_hd::where('yearkey',$year)->where('monthkey',$month)->first();


//        dd('hello world');
        $data = DB::table('inc_dts')
            ->select(
                'empqccode',
                DB::raw('MAX(grade) AS Grade'),
                DB::raw('MAX(CASE WHEN le_name = \'Very Easy\' THEN rate ELSE NULL END) AS RateVeryEasy'),
                DB::raw('MAX(CASE WHEN le_name = \'Easy\' THEN rate ELSE NULL END) AS RateEasy'),
                DB::raw('MAX(CASE WHEN le_name = \'Middling\' THEN rate ELSE NULL END) AS RateMiddling'),
                DB::raw('MAX(CASE WHEN le_name = \'Hard\' THEN rate ELSE NULL END) AS RateHard'),
                DB::raw('MAX(CASE WHEN le_name = \'Very Hard\' THEN rate ELSE NULL END) AS RateVeryHard'),
                'paystatus',
                'payremark',
                'payamnt',
                'qcqty AS EmpqcCount',
                'timepermonth AS HM',
                'timeperday AS HD'
            )
            ->where('inc_id', $IncHds->id)
            ->groupBy(
                'empqccode',
                'paystatus',
                'payremark',
                'payamnt',
                'qcqty',
                'timepermonth',
                'timeperday'
            )
            ->get();





        dd($data);
    }
}
