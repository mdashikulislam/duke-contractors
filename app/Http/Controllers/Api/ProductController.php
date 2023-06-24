<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyProduct;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\RoofType;
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
        $product = Product::where('id',$id);
        if ($exist->type == 'Material'){
            $product =  $product->with('categories')
                ->with(['items'=>function($s){
                $s->with('company');
                $s->whereHas('company');
            }])->whereHas('items',function ($s){
                $s->with('company');
                $s->whereHas('company');
            });
        }else{
            $product =  $product->with('item')->whereHas('item');
        }
        $product = $product->first();
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
            'product_data'=>['required','array'],
            'product_data.*.company_id'=>['required','numeric','min:1'],
            'product_data.*.unit_price'=>['required','numeric','min:1'],
            'formula'=>['nullable','string'],
            'dim_covers'=>['nullable','numeric'],
        ];
        if ($request->type == 'Material'){
            $rules['is_default'] =['nullable','numeric','between:0,1'];
            $rules['category']=['required','max:255','array'];
            $rules['category.*.name']=['required','in:'.implode(',',PRODUCT_CATEGORY)];
            $rules['wood_type']= ['required','in:None,Plywood,Fasica'];
            $rules['own_category']=['required','max:255','in:'.implode(',',PRODUCT_CATEGORY_OWN)];
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
                $product->dim_covers = $request->dim_covers;
            }
            $product->save();
            foreach ($request->product_data as $data){
                $companyProduct = new CompanyProduct();
                $companyProduct->product_id = $product->id;
                if ($request->type =='Material'){
                    $companyProduct->company_id = @$data['company_id'] ?? 0;
                }else{
                    $companyProduct->company_id =  0;
                }
                $companyProduct->unit_price = $data['unit_price'];
                $companyProduct->save();
            }
            if ($request->type == 'Material'){
                foreach ($request->category as $cat) {
                    $category = new ProductCategory();
                    $category->product_id = $product->id;
                    $category->name = $cat['name'];
                    $category->formula = @$cat['formula'];
                    $category->save();
                }
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
            'type'=>['required','max:255','in:'.implode(',',PRODUCT_TYPE)],
        ];
        if ($request->type == 'Material'){
            $rules['category'] = ['required','max:255','in:'.implode(',',PRODUCT_CATEGORY)];
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

    public function productList(Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'lead_id'=>['required','numeric','exists:\App\Models\Lead,id']
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

        $roofType = RoofType::where('lead_id',$request->lead_id)->first();
        if(empty($roofType)){
            return response()->json([
                'status' => false,
                'message' => 'You are not complete run estimate',
                'data' => null
            ]);
        }
        $category = [];
        if ($roofType->tile == 1){
            $category[] = 'Tile';
        }
        if ($roofType->metal == 1){
            $category[] = 'Metal';
        }
        if ($roofType->shingle == 1){
            $category[] = 'Shingle';
        }
        if ($roofType->flat == 1){
            $category[] = 'Flat';
        }
        if ($roofType->tpo == 1){
            $category[] = 'Tpo';
        }
        $defaultProduct = Product::selectRaw('products.*,lead_products.quantity')
            ->with(['item' => function ($q) use ($roofType) {
                $q->where('company_id', $roofType->company_id);
            }])
            ->leftJoin('lead_products', function ($s) use ($request) {
                $s->on('lead_products.product_id', '=', 'products.id');
                $s->where('lead_products.lead_id', $request->lead_id);
            })
            ->whereHas('item', function ($q) use ($roofType) {
                $q->where('company_id', $roofType->company_id);
            })
            ->where('products.is_default', 1)
            ->where('products.type', 'Material')
            ->groupBy('products.id')
            ->get();

        $material = [];
        if ($category){
            foreach ($category as $cs){
                $materialProduct = Product::selectRaw('products.*,lead_products.quantity')
                    ->leftJoin('lead_products',function ($s) use($cs,$request){
                        $s->on('lead_products.product_id','=','products.id');
                        $s->where('lead_products.category','=',$cs);
                        $s->where('lead_products.lead_id', $request->lead_id);
                    })
                    ->where('products.type','Material')
                    ->with(['item'=>function($s) use($roofType){
                        $s->where('company_id',$roofType->company_id);
                    }])
                    ->whereHas('item',function ($s) use($roofType){
                        $s->where('company_id',$roofType->company_id);
                    })
                    ->with(['category'=>function($s) use($cs){
                        $s->where('name',$cs);
                    }])
                    ->whereHas('category',function ($s) use($cs){
                        $s->where('name',$cs);
                    })
                    ->where('products.is_default',0)
                    ->groupBy('products.id')
                    ->get();
                if (!empty($materialProduct)){
                    foreach ($materialProduct as $product){
                        $material[] = $product;
                    }
                }
            }
        }
        $otherProducts = Product::selectRaw('products.*,lead_products.quantity')
            ->with('item')
            ->whereHas('item')
            ->leftJoin('lead_products',function ($s) use($request){
                $s->on('lead_products.product_id','=','products.id');
                $s->where('lead_products.lead_id', $request->lead_id);
            })
            ->where('products.type','!=','Material')
            ->groupBy('products.id')
            ->get();
        return response()->json([
           'status'=>true,
            'message'=>'',
           'data'=>[
                'default'=>$defaultProduct,
                'materialProduct'=>$material,
                'otherProducts'=>$otherProducts
           ]
        ]);
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
            'product_data'=>['required','array'],
            'formula'=>['nullable','string'],
            'dim_covers'=>['nullable','numeric']
        ];
        if ($request->type == 'Material'){
            $rules['category']=['required','max:255','array'];
            $rules['category.*.name']=['required','in:'.implode(',',PRODUCT_CATEGORY)];
            $rules['category.*.formula']=['nullable'];
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
                $product->dim_covers = $request->dim_covers;
            }
            $product->save();
            foreach ($request->product_data as $data){
                $companyProduct = new CompanyProduct();
                $companyProduct->product_id = $product->id;
                if ($request->type =='Material'){
                    $companyProduct->company_id = @$data['company_id'] ?? 0;
                }else{
                    $companyProduct->company_id =  0;
                }
                $companyProduct->unit_price = $data['unit_price'];
                $companyProduct->save();
            }
            if ($request->type =='Material') {
                ProductCategory::where('product_id', $id)->delete();
                foreach ($request->category as $cat) {
                    $category = new ProductCategory();
                    $category->product_id = $product->id;
                    $category->name = $cat['name'];
                    $category->formula = @$cat['formula'];
                    $category->save();
                }
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
            'company_id'=>['nullable','numeric','exists:\App\Models\Company,id'],
            'lead_id'=>['required','numeric','exists:\App\Models\Lead,id']
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
        $dataValue = [];
        $company = 0;
        $leadId = $request->lead_id;
        if ($request->company_id){
            $company = $request->company_id;
        }else{
            $company = Company::where('is_default',1)->first()->id;
        }
        $plywood = Product::selectRaw('products.*,lead_products.quantity')->with(['item'=>function($q) use($company){
            $q->where('company_id',$company);
        }])
        ->leftJoin('lead_products',function ($s) use($leadId){
            $s->on('lead_products.product_id','=','products.id');
            $s->where('lead_products.lead_id',$leadId);
        })
            ->whereHas('item',function ($q) use($company){
                $q->where('company_id',$company);
            })
            ->where('products.is_default',1)
            ->where('products.type','Material')
            ->where('products.wood_type','Plywood')
            ->groupBy('products.id')
            ->get();

        $fasica = Product::selectRaw('products.*,IF(COUNT(lead_products.id) > 0, "Yes", "No") as selected,lead_products.quantity')
        ->leftJoin('lead_products',function ($s) use($leadId){
            $s->on('lead_products.product_id','=','products.id');
            $s->where('lead_products.lead_id',$leadId);
        })
            ->with(['item'=>function($q) use($company){
            $q->where('company_id',$company);
        }])
            ->whereHas('item',function ($q) use($company){
                $q->where('company_id',$company);
            })

        ->where('products.is_default',1)
        ->where('products.type','Material')
        ->where('products.wood_type','Fasica')
            ->groupBy('products.id')
            ->get();
        $none = Product::selectRaw('products.*,lead_products.quantity')->with(['item'=>function($q) use($company){
            $q->where('company_id',$company);
        }])
        ->leftJoin('lead_products',function ($s) use($leadId){
            $s->on('lead_products.product_id','=','products.id');
            $s->where('lead_products.lead_id',$leadId);
        })
        ->whereHas('item',function ($q) use($company){
            $q->where('company_id',$company);
        })
        ->where('products.is_default',1)
        ->where('products.type','Material')
        ->where('products.wood_type','None')
        ->groupBy('products.id')
        ->get();
        $dataValue = [
            'plywood'=>$plywood,
            'fasica'=>$fasica,
            'none'=>$none
        ];

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
