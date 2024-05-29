<?php

namespace App\Http\Controllers;

use App\Models\inc_hd;
use Illuminate\Http\Request;

class IncHdAfterSaveContoller extends Controller
{
    //
    public function getDataLocal($year, $month,$status){
        $IncHds = inc_hd::where('yearkey',$year)->where('monthkey',$month)->get();
        dd($IncHds);
        dd('GetDataLocal', $year, $month, $status);
    }
}
