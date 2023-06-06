<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Lead;
use App\Models\LeadProduct;
use App\Models\Product;
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
                $leadProduct->type = "Material";
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

    public function runEstimateDetails($leadId,Request $request)
    {
        $lead = Lead::with('cityOfPermit')->whereHas('cityOfPermit')->where('id',$leadId)->first();
        if (empty($lead)){
            return response()->json([
                'status' => false,
                'message' => 'Lead not found',
                'data' => null
            ]);
        }
        $companyId = 0;
        $roofType = RoofType::where('lead_id',$leadId)->first();
        if (empty($roofType)){
            return response()->json([
                'status' => false,
                'message' => 'You need to run estimate first',
                'data' => null
            ]);
        }
        if ($request->company_id && intval($request->company_id)){
            $companyId = $request->company_id;
        }else{
            $companyId = $roofType->company_id;
        }

        $materialProduct = LeadProduct::with(['products'=>function($s) use($companyId){
            $s->with(['item'=>function($p) use($companyId){
                $p->where('company_id',$companyId);
            }]);
            $s->whereHas('item',function($p) use($companyId){
                $p->where('company_id',$companyId);
            });
        }])
            ->whereHas('products',function ($s) use($companyId){
                $s->whereHas('item',function($p) use($companyId){
                    $p->where('company_id',$companyId);
                });
            })
            ->where('type','Material')
            ->where('lead_id',$leadId)->get();

        $otherProduct = LeadProduct::with(['products'=>function($s) use($companyId){
            $s->with('item');
            $s->whereHas('item');
        }])
            ->whereHas('products',function ($s) use($companyId){
                $s->whereHas('item');
            })
            ->where('type','!=','Material')
            ->where('lead_id',$leadId)->get();
        return response()->json([
            'status' => true,
            'message' => '',
            'data' => [
                'lead'=>$lead,
                'roofType'=>$roofType,
                'materialProduct'=>$materialProduct,
                'otherProducts'=>$otherProduct
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

    public function editLeadDetails(Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'lead_id'=>['required','numeric','exists:\App\Models\Lead,id'],
            'company_id'=> ['required','numeric','exists:\App\Models\Company,id'],
            'material_product_data'=>['nullable','array'],
            'material_product_data.*.product_id'=>['required','numeric'],
            'material_product_data.*.quantity'=>['required','numeric'],
            'material_product_data.*.category'=>['required'],
            'other_product_data.'=>['nullable','array'],
            'other_product_data.*.product_id'=>['required','numeric'],
            'other_product_data.*.quantity'=>['required','numeric'],
            'other_product_data.*.type'=>['required','string'],
            'miscellaneous'=>['nullable','between:0,100'],
            'desire_profit'=>['nullable','between:0,100'],
            'seller_commission'=>['nullable','between:0,100'],
            'office_commission'=>['nullable','between:0,100'],
            'final_contract_price'=>['nullable','string'],
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
        $lead = Lead::where('id',$request->lead_id)->first();
        if (empty($lead)){
            return response()->json([
                'status' => false,
                'message' => 'Lead not found',
                'data' => null
            ]);
        }
        $roofType = RoofType::where('lead_id',$lead->id)->first();
        $roofType->miscellaneous = $request->miscellaneous;
        $roofType->desire_profit = $request->company_id;
        $roofType->seller_commission = $request->seller_commission;
        $roofType->office_commission = $request->office_commission;
        $roofType->final_contract_price = $request->final_contract_price;
        $roofType->company_id = $request->company_id;
        $roofType->save();
        if (!empty($request->material_product_data)){
            LeadProduct::where('lead_id',$lead->id)->where('type','Material')->delete();
            foreach ($request->material_product_data as $data){
                LeadProduct::create([
                    'lead_id' => $lead->id,
                    'product_id' => $data['product_id'],
                    'category' => $data['category'],
                    'type' => 'Material',
                    'quantity' => $data['quantity']
                ]);
            }
        }
        if (!empty($request->other_product_data)){
            LeadProduct::where('lead_id',$lead->id)->where('type','!=','Material')->delete();
            foreach ($request->other_product_data as $data){
                LeadProduct::create([
                    'lead_id' => $lead->id,
                    'product_id' => $data['product_id'],
                    'type' => $data['type'],
                    'quantity' => $data['quantity']
                ]);
            }
        }
        return response()->json([
            'status' => true,
            'message' => 'Update successful',
            'data' => null
        ]);
    }

    public function lowPriceCompany(Request $request)
    {

        $validator = \Validator::make($request->all(),[
            'product_data'=>['required','array'],
            'product_data.*.id'=> ['required','numeric','exists:\App\Models\Product,id'],
            'product_data.*.quantity'=> ['required','numeric'],
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

        $product_data = $request->product_data;
        $companies = Company::all();
        if (empty($companies)){
            return response()->json([
                'status' => false,
                'message' => 'No company found',
                'data' => null
            ]);
        }
        $total = [];
        foreach ($product_data as $key => $data){
            if (!empty($companies)){
                $companyWise = [];
                foreach ($companies as $company){
                    $companyId = $company->id;
                    $materialProduct = Product::where('type','Material')
                        ->with(['item'=>function($p) use($companyId){
                            $p->where('company_id',$companyId);
                            $p->with('company');
                        }])
                        ->whereHas('item',function ($p) use($companyId){
                            $p->where('company_id',$companyId);
                        })
                        ->where('id',$data['id'])
                        ->where('type','Material')->first();
                    $cost = (int)$data['quantity'] * (float)@$materialProduct->item->unit_price ?? 0;
                    $companyWise[$companyId] = $cost;
                }
                $total[] = $companyWise;
            }
        }
        $arrSum = [];
        if (!empty($total)){
            foreach ($companies as $cp){
                $arrSum[$cp->id] = array_sum(array_column($total, $cp->id));
            }
        }
        $minValue = min($arrSum);
        $minKey = array_search($minValue, $arrSum);
        $finalCompany = Company::where('id',$minKey)->first();
        return response()->json([
            'status' => true,
            'message' => '',
            'data' => [
                'company'=>[
                    'id'=>$finalCompany->id,
                    'name'=>$finalCompany->name,
                    'total'=>$minValue
                ]
            ]
        ]);

    }
}
