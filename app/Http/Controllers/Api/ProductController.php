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
    public function index(Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'type'=>['nullable','in:'.implode(',',PRODUCT_TYPE)]
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

        $products = Product::with('categories');
        if (isset($request->type) && $request->type == 'Material'){
            $products =  $products->with(['items'=>function($s){
                $s->with('company');
                $s->whereHas('company');
            }])
                ->whereHas('items')
                ->where('type','Material');
        }else{
            $products = $products->with(['item'=>function($s){
                 $s->selectRaw('id,product_id,unit_price');
            }])
                ->whereHas('item')
                ->where('type','!=','Material');
        }
        $products = $products->orderByDesc('products.created_at')->get();
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
        $rules = [
            'name'=>['required','max:255','string'],
            'type'=>['required','max:255','in:'.implode(',',PRODUCT_TYPE)],
            'category'=>['required','max:255','array'],
            'category.*'=>['in:'.implode(',',PRODUCT_CATEGORY)],
            'product_data'=>['required','array']
        ];

        $validator = \Validator::make($request->all(),$rules);
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

    public function searchProduct(Request $request)
    {
        $rules = [
            'keyword'=>['required','max:255'],
            'category'=>['required','max:255','in:'.implode(',',PRODUCT_CATEGORY)],
            'type'=>['required','max:255','in:'.implode(',',PRODUCT_TYPE)]
        ];
        if ($request->type == 'Material'){
            $rules['company_id'] = ['required','numeric','exists:\App\Models\Company,id'];
        }
        $validator = \Validator::make($request->all(),$rules);
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
        $keyword = $request->keyword;
        $category = $request->category;
        $type = $request->type;
        if ($type == 'Material'){
            $products = Product::selectRaw('products.id,products.name,products.type,company_products.unit_price,company_products.dim_covers,companies.name as company_name,company_products.id as company_product_id')
                ->join('product_categories','product_categories.product_id','=','products.id')
                ->join('company_products','company_products.product_id','=','products.id')
                ->join('companies','companies.id','=','company_products.company_id')
                ->where('product_categories.name',$category)
                ->where('companies.id',$request->company_id)
                ->where('products.type',$type)
                ->where('products.name','LIKE',"%$keyword%")
                ->groupBy('products.id')
                ->get();
        }else{
            $products = Product::selectRaw('products.id,products.name,products.type,company_products.unit_price,company_products.id as company_product_id')
                ->join('product_categories','product_categories.product_id','=','products.id')
                ->join('company_products','company_products.product_id','=','products.id')
                ->where('product_categories.name',$category)
                ->where('products.type',$type)
                ->where('products.name','LIKE',"%$keyword%")
                ->groupBy('products.id')
                ->get();
        }
        if ($products){
            return response()->json([
                'status'=>true,
                'message'=>'',
                'data'=>[
                    'products'=>$products
                ]
            ]);
        }else{
            return response()->json([
                'status'=>false,
                'message'=>'No product found',
                'data'=>null
            ]);
        }
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

    public function delete($id)
    {
        $product = Product::where('id',$id)->first();
        if (empty($product)){
            return response()->json([
                'status' => false,
                'message' => 'product not found',
                'data' => null
            ]);
        }
        $product->delete();
        if ($product->has('item')){
            $product->item()->delete();
        }
        if ($product->has('items')){
            $product->items()->delete();
        }
        return response()->json([
            'status' => true,
            'message' => 'Product delete successful',
            'data' => null
        ]);
    }
}
