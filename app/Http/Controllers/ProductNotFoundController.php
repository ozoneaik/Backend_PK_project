<?php

namespace App\Http\Controllers;

use App\Models\ProductNotFound;
use Illuminate\Http\Request;

class ProductNotFoundController extends Controller
{
    public function list($year,$month){
        $ProductNotFound = ProductNotFound::where('year',$year)->where('month',$month)->get();
        return response()->json([
            'ProductNotFound' => $ProductNotFound,
        ]);
    }
}
