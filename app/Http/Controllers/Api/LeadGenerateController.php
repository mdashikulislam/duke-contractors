<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
            'tile_current'=>['required','numeric','between:0,1'],
            'metal_current'=>['required','numeric','between:0,1'],
            'shingle_current'=>['required','numeric','between:0,1'],
            'flat_current'=>['required','numeric','between:0,1'],
            'roof_snap'=>['required','array'],
            'eagle_view'=>['required','array']
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
        $type = new RoofType();
        $type->lead_id = $request->id;
        $type->tile = $request->tile;
        $type->metal = $request->metal;
        $type->shingle = $request->shingle;
        $type->flat = $request->flat;
        $type->tile_current = $request->tile_current;
        $type->metal_current = $request->metal_current;
        $type->shingle_current = $request->shingle_current;
        $type->flat_current = $request->flat_current;
        $type->roof_snap = $request->roof_snap;
        $type->eagle_view = $request->eagle_view;
        $type->tax = $request->tax;
        $type->save();
        return  response()->json([
            'status'=>true,
            'message'=>'Estimate save successfully',
            'data'=>null
        ]);
    }
}
