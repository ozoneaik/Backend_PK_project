<?php

namespace App\Http\Controllers;

use App\Models\qc_workday;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QcWorkdayController extends Controller
{
    //

    public function list($year) : JsonResponse {
        // Fetch existing workdays
        $workdays = qc_workday::where('wo_year', $year)->get();
//        dd($workdays);

        // Create an array to hold all months
        $allMonths = [];
        for ($month = 1; $month <= 12; $month++) {
            $allMonths[$month] = [
                'id' => null,
                'wo_year' => $year,
                'wo_month' => $month,
                'workday' => '-',
                'created_at' => '-',
                'updated_at' => '-',
                'created_by' => '-',
                'updated_by' => '-',
            ];
        }

        // Update with existing workdays
        foreach ($workdays as $workday) {
            $allMonths[$workday->wo_month] = [
                'id' => $workday->id,
                'wo_year' => $workday->wo_year,
                'wo_month' => $workday->wo_month,
                'workday' => $workday->workday,
                'created_by' => $workday->created_by,
                'updated_by' => $workday->updated_by,
                'created_at' => $workday->created_at,
                'updated_at' => $workday->updated_at,
            ];
        }

        // Convert array to a list
        $result = array_values($allMonths);

        return response()->json([
            'workdays' => $result,
        ]);
    }


    public function store(Request $request): JsonResponse
    {
        $workday = $request->all();
        $check = qc_workday::where('wo_year', $workday['wo_year'])->where('wo_month', $workday['wo_month'])->first();
        try {
            if ($workday['workday'] <= 0){
                throw new \InvalidArgumentException('จำนวนวันต้องมากกว่า 0 วัน');
            }
            if ($check) {
                //update
                $check->workday = $workday['workday'];
                $check->updated_by = auth()->user()->name;
                $check->update();
            }else{
                //store
                $create = new qc_workday();
                $create->wo_year = $workday['wo_year'];
                $create->wo_month = $workday['wo_month'];
                $create->workday = $workday['workday'];
                $create->created_by = auth()->user()->name;
                $create->updated_by = '-';
                $create->save();
            }
            $message = "สร้างหรืออัพเดทจำนวนวันของเดือน ".$workday['wo_year']."/".$workday['wo_month'].' สำเร็จ';
            $status = 200;
        }catch (\InvalidArgumentException $exception){
            $message = $exception->getMessage();
            $status = 400;
        }
        return response()->json([
            'message' => $message
        ],$status);
    }
}
