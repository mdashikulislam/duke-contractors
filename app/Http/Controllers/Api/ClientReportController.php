<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerPayment;
use App\Models\InspectionResult;
use App\Models\Lead;
use Illuminate\Http\Request;

class ClientReportController extends Controller
{
    public function index(Request $request)
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
        $leadId = $request->lead_id;
        $lead = Lead::with('cityOfPermit')
            ->with('sellers')
            ->where('id',$leadId)->first();
        $customerPayments = CustomerPayment::where('lead_id',$leadId)->get();
        $inspectionResults = InspectionResult::where('lead_id',$leadId)->get();
        return response()->json([
            'status' => true,
            'message' => '',
            'data' => [
                'lead'=>$lead,
                'customerPayments'=>$customerPayments,
                'inspectionResults'=>$inspectionResults
            ]
        ]);
    }

    public function addUpdate(Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'lead_id'=>['required','numeric','exists:\App\Models\Lead,id'],
            'permits'=>['nullable','array'],
            'permits.*.amount'=>['required','between:1,99999999999'],
            'permits.*.company'=>['required','numeric','exists:\App\Models\other_companies,id'],
            'permits.*.status'=>['nullable','in:Paid,Pending'],
            'permits.*.date'=>['required','date_format:Y-m-d'],
            'trash'=>['nullable','array'],
            'trash.*.amount'=>['required','between:1,99999999999'],
            'trash.*.company'=>['required','numeric','exists:\App\Models\other_companies,id'],
            'trash.*.status'=>['nullable','in:Paid,Pending'],
            'trash.*.date'=>['required','date_format:Y-m-d'],
            'supplies'=>['nullable','array'],
            'supplies.*.invoice'=>['required','max:50'],
            'supplies.*.company'=>['required','numeric','exists:\App\Models\other_companies,id'],
            'supplies.*.amount'=>['required','between:1,99999999999'],
            'supplies.*.status'=>['nullable','in:Paid,Pending'],
            'supplies.*.date'=>['required','date_format:Y-m-d'],
            'labour'=>['nullable','array'],
            'labour.*.precio_por_sq'=>['required','between:1,99999999999'],
            'labour.*.company'=>['required','numeric','exists:\App\Models\other_companies,id'],
            'labour.*.amount'=>['required','between:1,99999999999'],
            'labour.*.status'=>['nullable','in:Paid,Pending'],
            'labour.*.date'=>['required','date_format:Y-m-d'],
            'labour.*.deck'=>['required','string'],

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
    }
}
