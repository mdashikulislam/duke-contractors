<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index()
    {

        $products = Product::orderByDesc('created_at')->get();
        return response()->json([
            'status' => true,
            'message' => '',
            'data' => [
                'products'=>$products
            ]
        ]);


    }

    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'name'=>['required','max:255','string','unique:products,name'],
            'category'=>['required','max:255','in:'.implode(',',PRODUCT_CATEGORY)]
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
        $product = new Product();
        $product->name = $request->name;
        $product->category = $request->category;
        if ($product->save()){
            $response = [
                'status' => true,
                'message' => '',
                'data' => [
                    'product'=>$product
                ]
            ];
        }else{
            $response = [
                'status' => false,
                'message' => 'Product not create',
                'data' => null
            ];
        }
        return response()->json($response);
    }

    public function edit($id,Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'name'=>['required','max:255','string',Rule::unique('products')->ignore('id')],
            'category'=>['required','max:255','in:'.implode(',',PRODUCT_CATEGORY)]
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
        $product  = Product::where('id',$id)->first();
        if (empty($product)){
            return response()->json([
                'status'=>false,
                'message'=>'Product not found',
                'data'=>null
            ]);
        }
        $product->name = $request->name;
        $product->category = $request->category;
        if ($product->save()){
            $response = [
                'status' => true,
                'message' => '',
                'data' => [
                    'product'=>$product
                ]
            ];
        }else{
            $response = [
                'status' => false,
                'message' => 'Product not create',
                'data' => null
            ];
        }
        return response()->json($response);
    }
}
