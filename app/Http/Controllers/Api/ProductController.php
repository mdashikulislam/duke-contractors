<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompanyProduct;
use App\Models\Product;
use App\Models\ProductCategory;
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
            'name'=>['required','max:255','string'],
            'type'=>['required','max:255','in:'.implode(',',PRODUCT_TYPE)],
            'category'=>['required','max:255','array'],
            'category.*'=>['in:'.implode(',',PRODUCT_CATEGORY)],
            'product_data'=>['required','array']
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
        \DB::beginTransaction();
        try {
            $product = new Product();
            $product->name = $request->name;
            $product->type = $request->type;
            $product->save();
            foreach ($request->product_data as $data){
                $companyProduct = new CompanyProduct();
                $companyProduct->product_id = $product->id;
                if ($request->type =='Material'){
                    $companyProduct->company_id = @$data['company_id'] ?? 0;
                    $companyProduct->dim_covers = @$data['dim_covers'] ?? null;
                }else{
                    $companyProduct->company_id =  0;
                    $companyProduct->dim_covers = null;
                }
                $companyProduct->unit_price = $data['unit_price'];
                $companyProduct->save();
            }
            foreach ($request->category as $cat){
                $category = new ProductCategory();
                $category->product_id = $product->id;
                $category->name = $cat;
                $category->save();
            }
            \DB::commit();
            $response = [
                'status' => true,
                'message' => 'Product added successful',
                'data' => null
            ];
        }catch (\Exception $exception){
            \DB::rollBack();
            $response = [
                'status' => false,
                'message' => $exception->getMessage(),
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
