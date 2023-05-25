<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Lead;
use App\Models\LeadProduct;
use App\Models\RoofType;
use Illuminate\Http\Request;

class LeadGenerateController extends Controller
{
    public function index()
    {

    }

    public function runEstimate(Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'lead_id'=>['required','numeric'],
            'tile'=>['required','numeric','between:0,1'],
            'metal'=>['required','numeric','between:0,1'],
            'shingle'=>['required','numeric','between:0,1'],
            'flat'=>['required','numeric','between:0,1'],
            'tpo'=>['required','numeric','between:0,1'],
            'tile_current'=>['required','numeric','between:0,1'],
            'metal_current'=>['required','numeric','between:0,1'],
            'shingle_current'=>['required','numeric','between:0,1'],
            'flat_current'=>['required','numeric','between:0,1'],
            'tpo_current'=>['required','numeric','between:0,1'],
            'slope_1'=>['required','numeric','between:1,12'],
            'slope_2'=>['required','numeric','in:0.5,1,1.5'],
            'iso'=>['required','in:Yes,No'],
            'deck_type'=>['required','exists:\App\Models\DeckType,id'],
            'roof_snap'=>['required'],
            'eagle_view'=>['required'],
            'tax'=>['required','between:0,100'],
            'product_data'=>['required','array'],
            'product_data.*.product_id'=>['required','numeric'],
            'product_data.*.quantity'=>['required','numeric'],
            'product_data.*.category'=>['required','string'],
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
        $check = RoofType::where('lead_id',$request->lead_id)->exists();
        if ($check){
            return  response()->json([
                'status'=>false,
                'message'=>'Already exist',
                'data'=>null
            ]);
        }
        \DB::beginTransaction();
        try {
            $type = new RoofType();
            $type->lead_id = $request->lead_id;
            $type->tile = $request->tile;
            $type->metal = $request->metal;
            $type->shingle = $request->shingle;
            $type->tpo = $request->tpo;
            $type->flat = $request->flat;
            $type->tile_current = $request->tile_current;
            $type->metal_current = $request->metal_current;
            $type->shingle_current = $request->shingle_current;
            $type->flat_current = $request->flat_current;
            $type->tpo_current = $request->tpo_current;
            $type->slope_1 = $request->slope_1;
            $type->slope_2 = $request->slope_2;
            $type->iso = $request->iso;
            $type->deck_type = $request->deck_type;
            $type->roof_snap = $request->roof_snap;
            $type->eagle_view = $request->eagle_view;
            $type->tax = $request->tax;
            $type->company_id = @Company::where('is_default',1)->first()->id ?? 1;
            $type->save();
            foreach ($request->product_data as $data){
                $leadProduct = new LeadProduct();
                $leadProduct->lead_id = $request->lead_id;
                $leadProduct->product_id = $data['product_id'];
                $leadProduct->quantity = $data['quantity'];
                $leadProduct->category = $data['category'];
                $leadProduct->type = "Material";
                $leadProduct->cost = 0;
                $leadProduct->save();
            }
            Lead::where('id',$request->lead_id)->update(['is_estimate'=>1]);
            \DB::commit();
            return  response()->json([
                'status'=>true,
                'message'=>'Estimate save successfully',
                'data'=>null
            ]);
        }catch (\Exception $exception){
            \DB::rollBack();
            return  response()->json([
                'status'=>false,
                'message'=>$exception->getMessage(),
                'data'=>null
            ]);
        }

    }

    public function runEstimateDetails($leadId)
    {
        $lead = Lead::where('id',$leadId)->first();
        if (empty($lead)){
            return response()->json([
                'status' => false,
                'message' => 'Lead not found',
                'data' => null
            ]);
        }
        $roofType = RoofType::where('lead_id',$leadId)->first();
        if (empty($roofType)){
            return response()->json([
                'status' => false,
                'message' => 'You need to run estimate first',
                'data' => null
            ]);
        }
        $companyId = $roofType->company_id;

//        $leadProduct = LeadProduct::selectRaw('lead_products.*')
//            ->join('products','products.id','=','lead_products.product_id')
//            ->join('')
//            ->where('lead_products.lead_id',$leadId)->get();


        $materialProduct = LeadProduct::with(['products'=>function($s) use($companyId){
            $s->with(['item'=>function($p) use($companyId){
                $p->where('company_id',$companyId);
            }]);
        }])
            ->whereHas('products',function ($s)use($companyId){
                $s->whereHas(['item'=>function($p) use($companyId){
                    $p->where('company_id',$companyId);
                }]);
            })
            ->where('type','Material')
            ->where('lead_id',$leadId)->get();
        return response()->json([
            'status' => true,
            'message' => '',
            'data' => [
                'roofType'=>$roofType,
                'materialProduct'=>$materialProduct
            ]
        ]);

    }
    public function editRunEstimate($id,Request $request)
    {
        $type = RoofType::where('id',$id)->first();
        if (empty($lead)){
            return  response()->json([
                'status'=>false,
                'message'=>'Roof not found',
                'data'=>null
            ]);
        }
        $validator = \Validator::make($request->all(),[
            'id'=>['required','numeric'],
            'tile'=>['required','numeric','between:0,1'],
            'metal'=>['required','numeric','between:0,1'],
            'shingle'=>['required','numeric','between:0,1'],
            'flat'=>['required','numeric','between:0,1'],
            'tpo'=>['required','numeric','between:0,1'],
            'tile_current'=>['required','numeric','between:0,1'],
            'metal_current'=>['required','numeric','between:0,1'],
            'shingle_current'=>['required','numeric','between:0,1'],
            'flat_current'=>['required','numeric','between:0,1'],
            'tpo_current'=>['required','numeric','between:0,1'],
            'slope_1'=>['required','numeric','between:1,12'],
            'slope_2'=>['required','numeric','in:0.5,1,1.5'],
            'iso'=>['required','in:Yes,No'],
            'deck_type'=>['required','exists:\App\Models\DeckType,id'],
            'roof_snap'=>['required'],
            'eagle_view'=>['required'],
            'tax'=>['required','between:0,100']
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

        $type->tile = $request->tile;
        $type->metal = $request->metal;
        $type->shingle = $request->shingle;
        $type->tpo = $request->tpo;
        $type->flat = $request->flat;
        $type->tile_current = $request->tile_current;
        $type->metal_current = $request->metal_current;
        $type->shingle_current = $request->shingle_current;
        $type->flat_current = $request->flat_current;
        $type->tpo_current = $request->tpo_current;
        $type->slope_1 = $request->slope_1;
        $type->slope_2 = $request->slope_2;
        $type->iso = $request->iso;
        $type->deck_type = $request->deck_type;
        $type->roof_snap = $request->roof_snap;
        $type->eagle_view = $request->eagle_view;
        $type->tax = $request->tax;
        $type->save();
        return  response()->json([
            'status'=>true,
            'message'=>'Estimate update successfully',
            'data'=>null
        ]);
    }
    public function addLeadPrice(Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'lead_id'=>['required','numeric','exists:\App\Models\Lead,id'],
            'company_product_id'=> ['required','numeric','exists:\App\Models\CompanyProduct,id'],
            'quantity'=>['required','numeric'],
            'category'=>['required','max:255','in:'.implode(',',PRODUCT_CATEGORY)],
            'type'=>['required','max:255','in:'.implode(',',PRODUCT_TYPE)],
            'cost'=>['required','between:0,99999999']
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
        $leadProduct = new LeadProduct();
        $leadProduct->lead_id = $request->lead_id;
        $leadProduct->company_product_id = $request->company_product_id;
        $leadProduct->quantity = $request->quantity;
        $leadProduct->category = $request->category;
        $leadProduct->type = $request->type;
        $leadProduct->cost = $request->cost;
        $leadProduct->tax_price = $request->tax_price ?? 0;
        if ($leadProduct->save()){
            $response = [
                'status' => true,
                'message' => 'Price added successfully',
                'data' => null
            ];
        }else{
            $response = [
                'status' => false,
                'message' => 'Price npt added',
                'data' => null
            ];
        }
        return response()->json($response);
    }
}
