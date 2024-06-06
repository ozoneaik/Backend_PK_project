<?php

namespace App\Http\Controllers;

use App\Models\inc_hd;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApproveIncHdController extends Controller
{
    public function ApproveByQC(Request $request){
//        dd(request()->all());
        DB::beginTransaction();
        $IncHD = inc_hd::find($request->inc_id);

        if ($IncHD){
            $IncHD->status = 'wait';
            $IncHD->confirmdate = Carbon::now();
            if ($IncHD->save()){
                DB::commit();
                return response()->json(['msg' => 'ส่งอนุมัติสำเร็จ กรุณารอ อนุมัติ'],200);
            }else{
                DB::rollBack();
                return response()->json(['msg' => 'ส่งอนุมัติไม่สำเร็จ'],400);
            }

        }else{
            return response()->json(['msg' => 'ไม่เจอข้อมูลในฐานข้อมูล ติดต่อ IT'],400);
        }
    }

    public function ApproveByHR(Request $request){
//        dd(request()->all());
        DB::beginTransaction();
        $IncHD = inc_hd::find($request->inc_id);

        if ($IncHD){
            $IncHD->confirmapprove = Carbon::now();
            $IncHD->confirmapprovebycode = auth()->user()->name.'( '.auth()->user()->authcode.' )';
            $IncHD->status = 'approve';
            if ($IncHD->save()){
                DB::commit();
                return response()->json(['msg' => 'อนุมัติสำเร็จ กรุณารอ อนุมัติ'],200);
            }else{
                DB::rollBack();
                return response()->json(['msg' => 'อนุมัติไม่สำเร็จ'],400);
            }

        }else{
            return response()->json(['msg' => 'ไม่เจอข้อมูลในฐานข้อมูล ติดต่อ IT'],400);
        }
    }

    public function ConfirmPayDate(Request $request){
        DB::beginTransaction();
        $IncHD = inc_hd::find($request->inc_id);

        if ($IncHD){
            $IncHD->confirmpaydate = Carbon::now();
            $IncHD->confirmpaydatebycode = auth()->user()->name.'( '.auth()->user()->authcode.' )';
            $IncHD->status = 'complete';
            if ($IncHD->save()){
                DB::commit();
                return response()->json(['msg' => 'ยืนยันการจ่ายสำเร็จ กรุณารอ อนุมัติ'],200);
            }else{
                DB::rollBack();
                return response()->json(['msg' => 'ยืนยันการจ่ายไม่สำเร็จ'],400);
            }

        }else{
            return response()->json(['msg' => 'ไม่เจอข้อมูลในฐานข้อมูล ติดต่อ IT'],400);
        }
    }
}
