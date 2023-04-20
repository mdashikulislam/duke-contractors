<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;

class LeadControllerController extends Controller
{
    public function getLead(Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'offset'=>['nullable','numeric'],
            'limit'=>['nullable','numeric']
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
        $limit = 20;
        $offset = 0;
        if (!empty($request->limit)){
            $limit = $request->limit;
        }
        if (!empty($request->offset)){
            $offset = $request->offset;
        }
        $leads = Lead::with('jobTypes')->whereHas('jobTypes')->orderByDesc('created_at')->skip($offset)->limit($limit)->get();
        return response()->json([
            'status'=>true,
            'message'=>'',
            'data'=>[
                'leads'=>$leads
            ]
        ]);

    }
    public function addLead(Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'client_name'=>['required','max:191'],
            'address'=>['required','max:191'],
            'phone'=>['required','max:191'],
            'email'=>['required','max:191'],
            'additional_comments'=>['nullable','max:191'],
            'job_type'=>['required','numeric','exists:\App\Models\JobType,id'],
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
        $lead = new Lead();
        $lead->user_id = getAuthInfo()->id;
        $lead->client_name = $request->client_name;
        $lead->address = $request->address;
        $lead->phone = $request->phone;
        $lead->job_type = $request->job_type;
        $lead->email = $request->email;
        $lead->additional_comments = $request->additional_comments;
        $lead->price_of_quote = 0;
        $lead->status = 'Not Sent';
        $lead->save();
        return response()->json([
           'status'=>true,
           'message'=>'Lead added successful',
           'data'=>null,
        ]);
    }
}
