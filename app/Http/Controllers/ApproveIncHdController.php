<?php

namespace App\Http\Controllers;

use App\Models\inc_hd;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApproveIncHdController extends Controller
{
    public function ApproveByHR(Request $request){
//        dd(request()->all(),'send approve by HR');
        DB::beginTransaction();
        $IncHD = inc_hd::find($request->inc_id);

        if ($IncHD){
            $IncHD->status = 'wait';
            $IncHD->confirmdate = Carbon::now();
            if ($IncHD->save()){
                DB::commit();
                return response()->json(['message' => 'กรุณารอ QC อนุมัติ'],200);
            }else{
                DB::rollBack();
                return response()->json(['message' => 'ส่งอนุมัติไม่สำเร็จ'],400);
            }

        }else{
            return response()->json(['message' => 'ไม่เจอข้อมูลในฐานข้อมูล ติดต่อ IT'],400);
        }
    }

    public function ApproveByQC(Request $request){
//        dd(request()->all(), 'send approve by QC');
        DB::beginTransaction();
        $IncHD = inc_hd::find($request->inc_id);

        if ($IncHD){
            $IncHD->confirmapprove = Carbon::now();
            $IncHD->confirmapprovebycode = '( '.auth()->user()->authcode.' ) ' . auth()->user()->name;
            $IncHD->status = 'approve';
            if ($IncHD->save()){
                DB::commit();
                return response()->json(['message' => 'ส่งรายงานเพื่อให้ HR อนุมัติการจ่ายแล้ว'],200);
            }else{
                DB::rollBack();
                return response()->json(['message' => 'อนุมัติไม่สำเร็จ'],400);
            }

        }else{
            return response()->json(['message' => 'ไม่เจอข้อมูลในฐานข้อมูล ติดต่อ IT'],400);
        }
    }

    public function ConfirmPayDate(Request $request){
        DB::beginTransaction();
        $IncHD = inc_hd::find($request->inc_id);

        if ($IncHD){
            $IncHD->confirmpaydate = Carbon::now();
            $IncHD->confirmpaydatebycode = '( '.auth()->user()->authcode.' ) '.auth()->user()->name;
            $IncHD->status = 'complete';
            if ($IncHD->save()){
                DB::commit();
                return response()->json(['message' => 'ยืนยันการจ่ายสำเร็จ'],200);
            }else{
                DB::rollBack();
                return response()->json(['message' => 'ยืนยันการจ่ายไม่สำเร็จ'],400);
            }

        }else{
            return response()->json(['message' => 'ไม่เจอข้อมูลในฐานข้อมูล ติดต่อ IT'],400);
        }
    }
}
