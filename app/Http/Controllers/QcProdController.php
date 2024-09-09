<?php

namespace App\Http\Controllers;

use App\Http\Requests\QcProductRequest;
use App\Models\ProductNotFound;
use App\Models\qc_prod;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class QcProdController extends Controller
{

    public function index(): JsonResponse
    {
        $products = qc_prod::all();
        if (count($products) > 0){
            return response()->json(['products' => $products,'msg' => 'ตรวจพบรายการสินค้าในฐานข้อมุล'], 200);
        }else{
            return response()->json(['products' => null,'msg' => 'ไม่พบรายการสินค้าในฐานข้อมุล'], 400);
        }
    }


    public function store(QcProductRequest $request): JsonResponse
    {
        $timeperpcs = (string)$request->timeperpcs;
        $products = new qc_prod();
        $products->pid = $request->pid;
        $products->pname = $request->pname;
        $products->levelid = $request->levelid;
        $products->timeperpcs = $timeperpcs;
        $products->createdate = Carbon::now();
        $products->createby = auth()->user()->name;
        $products->updateby = auth()->user()->name;
        $products->updatedate = Carbon::now();
        $products->save();

        return response()->json([
            'message' => 'เพิ่มสินค้า qc สำเร็จ'
        ], 200);
    }

    public function edit($id) : JsonResponse
    {
        $product = qc_prod::findOrFail($id);
        if ($product) {
            return response()->json([
                'product' => $product
            ], 200);
        }
        return response()->json([
            'message' => 'ไม่พบรายการสินค้าที่ต้องการแก้ไข'
        ], 400);
    }

    public function update(QcProductRequest $request, $id) : JsonResponse
    {
        try {
            $product = qc_prod::find($id);
            $product->levelid = $request->levelid;
            $product->timeperpcs = $request->timeperpcs;
            $product->updatedate = Carbon::now();
            $product->save();
            $message = 'อัพเดทข้อมูลสินค้าสำเร็จ';
            $status = 200;
        }catch (\Exception $exception){
            $status = 400;
            $message = $exception->getMessage();
        }
        return response()->json([
            'message' => $message,
        ],$status);
    }

    public function notFound($year,$month) : JsonResponse{
        $notFound = ProductNotFound::where('year',$year)->where('month',$month)->get();
        return response()->json([
            'list' => $notFound,
        ]);
    }
}
