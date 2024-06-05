<?php

namespace App\Http\Controllers;

use App\Models\qc_rate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QcRateController extends Controller
{
    //
    public function getRate(){
        $rates = DB::table('qc_rates')
            ->join('qc_levels', 'qc_rates.le_id', '=', 'qc_levels.le_id')
            ->select('qc_rates.*', 'qc_levels.le_code','qc_levels.le_name')
            ->orderBy('qc_rates.ra_id', 'asc')
            ->get();
        if($rates){
            return response()->json(['rates' => $rates, 'msg' => 'ดึงข้อมูลสำเร็จ'],200);
        }else{
            return response()->json(['rates' => null, 'msg' => 'ไม่พบข้อมูล'],400);
        }
    }

    public function update(Request $request){
        $requestData = $request->all();

        try {
            foreach ($requestData['rates'] as $rateData) {
                $rate = qc_rate::where('ra_id', $rateData['ra_id'])->first();
                if ($rate) {
                    $rate->update([
                        'rate' => $rateData['rate']
                    ]);
                }
            }
            return response()->json(['message' => 'สำเร็จ'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'ไม่สำเร็จ'], 400);
        }
    }
}
