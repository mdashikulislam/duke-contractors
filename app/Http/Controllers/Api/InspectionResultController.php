<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InspectionResult;
use Illuminate\Http\Request;

class InspectionResultController extends Controller
{
    public function index(Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'lead_id'=>['required','numeric','exists:\App\Models\Lead,id'],
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
        $results = InspectionResult::where('lead_id',$request->lead_id)->orderByDesc('created_at')->get();
        if (empty($payments)){
            return response()->json([
                'status' => false,
                'message' => 'No inspection result found',
                'data' => null
            ]);
        }else{
            return response()->json([
                'status' => true,
                'message' => '',
                'data' => [
                    'results'=>$results
                ]
            ]);
        }
    }
    public function create(Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'lead_id'=>['required','numeric','exists:\App\Models\Lead,id'],
            'type'=>['required','string','max:191'],
            'status'=>['required','in:Active,Inactive'],
            'date'=>['required','date_format:Y-m-d']
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
        $result = new InspectionResult();
        $result->lead_id = $request->lead_id;
        $result->user_id = auth()->guard('api')->id();
        $result->type = $request->type;
        $result->status = $request->status;
        $result->date = $request->date;
        if ($result->save()){
            return response()->json([
                'status' => true,
                'message' => 'Inspection Result save successfully',
                'data' => null
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Inspection Result not saved',
                'data' => null
            ]);
        }
    }
    public function edit($id,Request $request)
    {
        $result = InspectionResult::where('id',$id)->first();
        if (empty($result)){
            return response()->json([
                'status' => false,
                'message' => 'No record found',
                'data' => null
            ]);
        }
        $validator = \Validator::make($request->all(),[
            'lead_id'=>['required','numeric','exists:\App\Models\Lead,id'],
            'type'=>['required','string','max:191'],
            'status'=>['required','in:Active,Inactive'],
            'date'=>['required','date_format:Y-m-d']
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
        $result->lead_id = $request->lead_id;
        $result->user_id = auth()->guard('api')->id();
        $result->type = $request->type;
        $result->status = $request->status;
        $result->date = $request->date;
        if ($result->save()){
            return response()->json([
                'status' => true,
                'message' => 'Inspection Result update successfully',
                'data' => null
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Inspection Result not update',
                'data' => null
            ]);
        }
    }
    public function delete(Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'id'=>['required','numeric','exists:\App\Models\InspectionResult,id']
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
        InspectionResult::where('id',$request->id)->delete();
        return response()->json([
            'status' => true,
            'message' => 'Record delete successful',
            'data' => null
        ]);
    }
}
