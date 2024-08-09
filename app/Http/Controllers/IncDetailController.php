<?php

namespace App\Http\Controllers;

use App\Models\inc_detail;
use App\Models\ProductNotFound;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class IncDetailController extends Controller
{
    //
    public function store($IncHdId, $month, $year, $empqccode) : bool
    {

        $results = DB::connection('mysql_main_qc')->table('qc_log_data as ld')
            ->leftJoin('qc_prod as p', 'ld.skucode', '=', 'p.pid')
            ->leftJoin('qc_level as lv', 'p.levelid', '=', 'lv.levelid')
            ->leftJoin('qc_user as qu', 'ld.empqc', '=', 'qu.emp_no')
            ->select(
                'qu.emp_no',
                'qu.emp_name',
                'ld.skucode',
                'p.pname',
                DB::raw("
            CASE
                WHEN lv.levelname = 'Very easy' THEN 'Very easy'
                WHEN lv.levelname = 'Easy' THEN 'Easy'
                WHEN lv.levelname = 'Middling' THEN 'Middling'
                WHEN lv.levelname = 'Hard' THEN 'Hard'
                WHEN lv.levelname = 'Very Hard' THEN 'Very Hard'
                WHEN lv.levelname = 'No QC' THEN 'No QC'
                ELSE '-'
            END AS le_name
        "),
                'lv.levelid',
                'p.timeperpcs',
                DB::raw('COUNT(*) AS skuqty')
            )
            ->whereBetween('datekey', ["$year-$month-01", DB::raw("LAST_DAY('$year-$month-01')")])
            ->where('qu.emp_no', $empqccode)
            ->groupBy(
                'qu.emp_no',
                'qu.emp_name',
                'ld.skucode',
                'p.pname',
                'le_name',
                'lv.levelid',
                'p.timeperpcs'
            )
            ->orderBy('le_name')
            ->get();

        foreach ($results as $index => $result) {
            $IncDetail = new inc_detail();
            $IncDetail->inc_id = $IncHdId;
            $IncDetail->monthkey = $month;
            $IncDetail->empqccode = $empqccode;
            $IncDetail->skucode = $result->skucode;
            // กรณีไม่เจอชื่อสินค้า
            if (!$result->pname) {
                $not_founds = new ProductNotFound();
                $not_founds->skucode = $result->skucode;
                $not_founds->skuname = 'ไม่พบสินค้า';
                $not_founds->year = $year;
                $not_founds->month = $month;
                $IncDetail->skuname = 'ไม่มีชื่อสินค้า';
                $IncDetail->le_id = 6;
                $IncDetail->le_name = 'L006';
                $IncDetail->timeperpcs = Carbon::createFromTimestamp('00:00:00')->format('H:i:s');
                $not_founds->save();
            } else {
                $IncDetail->skuname = $result->pname;
                $ConvertLevelId = substr($result->levelid, 3);
                $IncDetail->le_id = $ConvertLevelId;
                $IncDetail->le_name = $result->le_name;
                $IncDetail->timeperpcs = Carbon::createFromTimestamp($result->timeperpcs)->format('H:i:s');
            }
            $IncDetail->skuqty = $result->skuqty;
            $IncDetail->save();
        }
        return true;
    }

    public function update() : int
    {
        return 0;
    }
}

