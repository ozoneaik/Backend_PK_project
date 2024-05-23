<?php

namespace App\Http\Controllers;

use App\Http\Requests\QcProductRequest;
use App\Models\qc_prod;
use Illuminate\Support\Facades\Auth;

class QcProdController extends Controller
{

    public function index(){
        $products = qc_prod::all();
        return response()->json($products);
    }


    public function store(QcProductRequest $request){

        $difficultyLevels = [
            'Very Easy' => 1,
            'Easy' => 2,
            'Middling' => 3,
            'Hard' => 4,
            'Very Hard' => 5,
        ];

        $le_id = $difficultyLevels[$request->le_id] ?? 6;

        $time = (string)$request->timeperpcs;
        $products = new qc_prod();
        $products->pid = $request->pid;
        $products->pname = $request->pname;
        $products->le_id = $le_id;
        $products->timeperpcs = $time;
        $products->createbycode = Auth::user()->emp_no;
        $products->updatebycode = Auth::user()->emp_no;
        $products->save();

        return  response()->json([
            'message' => 'เพิ่มสินค้า qc สำเร็จ'
        ],200);
    }

    public function edit($id){
        $product = qc_prod::findOrFail($id);
        if ($product){
            return response()->json(['product' => $product],200);
        }
        return response()->json([],400);
    }

    public function update(QcProductRequest $request,$id){

        return response()->json(['hello',$id]);
    }
}
