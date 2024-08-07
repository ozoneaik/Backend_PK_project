<?php

namespace App\Http\Controllers;

use App\Http\Requests\QcProductRequest;
use App\Models\ProductNotFound;
use App\Models\qc_prod;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QcProdController extends Controller
{

    public function index()
    {

//        $products = DB::connection('mysql_main_qc')
//            ->table('qc_prod')
//            ->join('qc_level', 'qc_prod.levelid', '=', 'qc_level.levelid')
//            ->get();
        $products = qc_prod::all();
        if (count($products) > 0){
            return response()->json(['products' => $products,'msg' => 'ตรวจพบรายการสินค้าในฐานข้อมุล'], 200);
        }else{
            return response()->json(['products' => null,'msg' => 'ไม่พบรายการสินค้าในฐานข้อมุล'], 400);
        }
    }


    public function store(QcProductRequest $request)
    {
//        dd($request->all());
        $timeperpcs = (string)$request->timeperpcs;
        $products = new qc_prod();
        $products->pid = $request->pid;
        $products->pname = $request->pname;
        $products->levelid = $request->levelid;
        $products->timeperpcs = $timeperpcs;
        $products->createdate = Carbon::now();
        $products->updatedate = Carbon::now();
        $products->save();

        return response()->json([
            'message' => 'เพิ่มสินค้า qc สำเร็จ'
        ], 200);
    }

    public function edit($id)
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

    public function update(QcProductRequest $request, $id)
    {

        $product = qc_prod::find($id);
        $product->levelid = $request->levelid;
        $product->timeperpcs = $request->timeperpcs;
        $product->updatedate = Carbon::now();
        $product->save();
        return response()->json(['hello', $id]);
    }

    public function notFound($year,$month){
        $notFound = ProductNotFound::where('year',$year)->where('month',$month)->get();
        return response()->json([
            'list' => $notFound,
        ]);
    }
}
