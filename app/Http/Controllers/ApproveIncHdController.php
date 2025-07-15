<?php

namespace App\Http\Controllers;

use App\Models\inc_dt;
use App\Models\inc_hd;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApproveIncHdController extends Controller
{
    public function ApproveByQC(Request $request)
    {
        //        dd(request()->all(),'send approve by HR');
        DB::beginTransaction();
        $IncHD = inc_hd::find($request->inc_id);

        if ($IncHD) {
            $IncHD->status = 'wait';
            $IncHD->confirmdate = Carbon::now();
            if ($IncHD->save()) {
                DB::commit();
                return response()->json(['message' => 'กรุณารอ HR อนุมัติ'], 200);
            } else {
                DB::rollBack();
                return response()->json(['message' => 'ส่งอนุมัติไม่สำเร็จ'], 400);
            }
        } else {
            return response()->json(['message' => 'ไม่เจอข้อมูลในฐานข้อมูล ติดต่อ IT'], 400);
        }
    }

    public function ApproveByHR(Request $request)
    {
        //        dd(request()->all(), 'send approve by QC');
        DB::beginTransaction();
        $IncHD = inc_hd::find($request->inc_id);

        if ($IncHD) {
            $IncHD->confirmapprove = Carbon::now();
            $IncHD->confirmapprovebycode = '( ' . auth()->user()->authcode . ' ) ' . auth()->user()->name;
            $IncHD->status = 'approve';
            if ($IncHD->save()) {
                DB::commit();
                return response()->json(['message' => 'อนุมัติแล้ว รอยืนยันการจ่าย'], 200);
            } else {
                DB::rollBack();
                return response()->json(['message' => 'อนุมัติไม่สำเร็จ'], 400);
            }
        } else {
            return response()->json(['message' => 'ไม่เจอข้อมูลในฐานข้อมูล ติดต่อ IT'], 400);
        }
    }

    public function ConfirmPayDate(Request $request)
    {
        DB::beginTransaction();
        $IncHD = inc_hd::find($request->inc_id);

        if ($IncHD) {
            $IncHD->confirmpaydate = Carbon::now();
            $IncHD->confirmpaydatebycode = '( ' . auth()->user()->authcode . ' ) ' . auth()->user()->name;
            $IncHD->status = 'complete';
            if ($IncHD->save()) {
                DB::commit();
                return response()->json(['message' => 'ยืนยันการจ่ายสำเร็จ'], 200);
            } else {
                DB::rollBack();
                return response()->json(['message' => 'ยืนยันการจ่ายไม่สำเร็จ'], 400);
            }
        } else {
            return response()->json(['message' => 'ไม่เจอข้อมูลในฐานข้อมูล ติดต่อ IT'], 400);
        }
    }

    public function updatePayStatus(Request $request)
    {
        $request->validate([
            'inc_id' => 'required|integer',
            'list' => 'required|array',
        ], [
            'inc_id.required' => 'ไม่พบข้อมูล Inc_ID',
            'list.required' => 'ไม่พบ List ของข้อมูล',
        ]);
        $inc_id = $request->inc_id;
        $list = $request->list;
        try {
            DB::beginTransaction();
            foreach ($list as $key => $l) {
                inc_dt::query()->where('inc_id', $inc_id)
                    ->where('empqccode', $l['empqc'])
                    ->update([
                        'paystatus' => $l['paystatus'],
                        'payremark' => $l['payremark'] ?? '',
                    ]);
            }
            DB::commit();
            return response()->json(['message' => 'อัพเดทสถานะการจ่ายสำเร็จ'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'req' => $request->all(),
            ], 400);
        }
    }
}
