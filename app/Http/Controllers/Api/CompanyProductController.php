<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompanyProduct;
use Illuminate\Http\Request;

class CompanyProductController extends Controller
{
    public function index()
    {

    }

    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'company_id'=>['required','numeric'],
            'product_id'=>['required','numeric'],
            'dim_covers'=>['nullable','numeric'],
            'unit_price'=>['required','numeric'],
            'tax'=>['required','numeric'],
        ]);
        if ($validator->fails()){
            $errors = "";
            $e = $validator->errors()->all();
            foreach ($e as $error) {
                $errors .= $error . "\n";
            }
            $response = [
                'status' => false,
                'message' => $errors,
                'data' => null
            ];
            return response()->json($response);
        }
        $product = new CompanyProduct();
        $product->product_id = $request->product_id;
        $product->company_id = $request->company_id;
        $product->dim_covers = $request->dim_covers;
        $product->unit_price = $request->unit_price;
        $product->tax = $request->tax;
        if ($product->save()){
            $response = [
                'status' => false,
                'message' => '',
                'data' => null
            ];
        }else{
            $response = [
                'status' => false,
                'message' => 'Product not save',
                'data' => null
            ];
        }
        return response()->json($response);
    }
}
