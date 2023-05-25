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

    public function productDetails($id)
    {
        $exist = Product::where('id',$id)->first();
        if (empty($exist)){
            return response()->json([
                'status' => false,
                'message' => 'Product not found',
                'data' => null
            ]);
        }
        $product = Product::with('categories')->whereHas('categories');
        if ($exist->type == 'Material'){
            $product =  $product->with(['items'=>function($s){
                $s->with('company');
                $s->whereHas('company');
            }])->whereHas('items',function ($s){
                $s->with('company');
                $s->whereHas('company');
            });
        }else{
            $product =  $product->with('item')->whereHas('item');
        }
        $product = $product->where('id',$id)->first();
        return response()->json([
            'status'=>true,
            'message'=>'',
            'data'=>[
                'product'=>$product
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
        if ($request->tyep == 'Material'){
            $rules['wood_type']= ['required','in:None,Plywood,Fasica'];
            $rules['own_category']=['required','max:255','in:'.implode(',',PRODUCT_CATEGORY_OWN)];
            $rules['is_default'] =['nullable','numeric','between:0,1'];
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
        \DB::beginTransaction();
        try {
            $product = new Product();
            $product->name = $request->name;
            $product->type = $request->type;
            if ($request->type == 'Material'){
                $product->is_default = $request->is_default ?? 0;
                $product->wood_type = $request->wood_type ?? 'None';
                $product->product_categoty = $request->own_category;
            }
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
            $rules['own_category'] = ['nullable','string','in:'.implode(',',PRODUCT_CATEGORY_OWN)];
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
                ->where('products.type',$type);
            if ($request->own_category){
                $products = $products->where('product_categoty',$request->own_category);
            }
            $products = $products->where('products.name','LIKE',"%$keyword%")
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
        $product = Product::where('id',$id)->first();
        if (empty($product)){
            return response()->json([
                'status' => false,
                'message' => 'Product not found',
                'data' => null
            ]);
        }

        $rules = [
            'name'=>['required','max:255','string'],
            'type'=>['required','max:255','in:'.implode(',',PRODUCT_TYPE)],
            'category'=>['required','max:255','array'],
            'category.*'=>['in:'.implode(',',PRODUCT_CATEGORY)],
            'product_data'=>['required','array']
        ];
        if ($request->tyep == 'Material'){
            $rules['wood_type']= ['required','in:None,Plywood,Fasica'];
            $rules['own_category']=['required','max:255','in:'.implode(',',PRODUCT_CATEGORY_OWN)];
            $rules['is_default'] =['nullable','numeric','between:0,1'];
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

        \DB::beginTransaction();
        try {
            $product->name = $request->name;
            $product->type = $request->type;
            if ($request->type == 'Material'){
                $product->is_default = $request->is_default ?? 0;
                $product->wood_type = $request->wood_type ?? 'None';
                $product->product_categoty = $request->own_category;
            }
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
            ProductCategory::where('product_id',$id)->delete();
            foreach ($request->category as $cat){
                $category = new ProductCategory();
                $category->product_id = $product->id;
                $category->name = $cat;
                $category->save();
            }
            \DB::commit();
            $response = [
                'status' => true,
                'message' => 'Product update successful',
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

    public function getDefaultProduct(Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'category'=>['required','array'],
            'category.*'=>['in:'.implode(',',PRODUCT_CATEGORY)]
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
        $category = $request->category;
        $dataValue = [];
        foreach ($category as $cat){
            $plywood = Product::whereHas('categories',function ($q) use ($cat){
                $q->where('name',$cat);
            })
                ->where('is_default',1)
                ->where('type','Material')->where('wood_type','Plywood')->get();

            $fasica = Product::whereHas('categories',function ($q) use ($cat){
                $q->where('name',$cat);
            })
                ->where('is_default',1)
                ->where('type','Material')->where('wood_type','Fasica')->get();
            $none = Product::whereHas('categories',function ($q) use ($cat){
                $q->where('name',$cat);
            })
                ->where('is_default',1)
                ->where('type','Material')->where('wood_type','None')->get();

            $newData = [
                'plywood'=>$plywood,
                'fasica'=>$fasica,
                'none'=>$none
            ];
            $dataValue[$cat] = $newData;
        }

        return response()->json([
            'status' => true,
            'message' => '',
            'data' => [
                $dataValue
            ]
        ]);
    }

    public function productOwnCategory()
    {
        return response()->json([
            'status' => true,
            'message' => '',
            'data' => [
                'ownCategories'=>PRODUCT_CATEGORY_OWN
            ]
        ]);
    }
}
