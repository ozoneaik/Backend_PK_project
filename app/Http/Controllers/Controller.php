<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function index(){
        $results = DB::connection('mysql_main_qc')->table('qc_log_data')
            ->select(
                DB::raw("DATE_FORMAT(datekey, '%Y-%m') AS year"),
                DB::raw('COUNT(job_id) as job_count'),
                DB::raw('COUNT(DISTINCT empqc) AS user_count')
            )
            ->where('datekey', 'LIKE', '2024-%-%')
            ->groupBy(DB::raw("DATE_FORMAT(datekey, '%Y-%m')"))
            ->get();
//        dd($results);
        $jsonEn = json_encode($results);

        return response()->json($jsonEn, 200);

    }

    public function test() : JsonResponse{
        return response()->json([
            'message' => 'success'
        ]);
    }
}
