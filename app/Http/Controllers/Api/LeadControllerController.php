<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobType;
use App\Models\Lead;
use App\Models\RoofType;
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
        $leads = Lead::myRole()->with('jobTypes')->whereHas('jobTypes')->orderByDesc('created_at')->skip($offset)->limit($limit)->get();
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
            'seller_name'=>['required','max:191'],
            'customer_name'=>['required','max:191'],
            'address'=>['required','max:191'],
            'phone'=>['required','max:191'],
            'email'=>['required','max:191'],
            'additional_comments'=>['nullable','max:191'],
            'job_type'=>['required','array'],
            'city_for_permit'=>['required','in:'.implode(',',CITY_LIST)]
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
        $jobType = [];
        foreach ($request->job_type as $type){
            if (gettype($type) == 'integer'){
                $exist = JobType::where('id',$type)->first();
                if (empty($exist)){
                    continue;
                }
                $jobType[] = $exist->id;
            }elseif (gettype($type) == 'string'){
                $create = JobType::firstOrCreate(['name' => $type]);
                $jobType[] = $create->id;
            }else{
                continue;
            }
        }
        $lead = new Lead();
        $lead->user_id = getAuthInfo()->id;
        $lead->customer_name = $request->customer_name;
        $lead->city_for_permit = $request->city_for_permit;
        $lead->seller_name = $request->seller_name;
        $lead->address = $request->address;
        $lead->phone = $request->phone;
        $lead->email = $request->email;
        $lead->additional_comments = $request->additional_comments;
        $lead->price_of_quote = 0;
        $lead->status = 'Not Sent';
        $lead->save();
        $lead->jobTypes()->sync($jobType);
        return response()->json([
           'status'=>true,
           'message'=>'Lead added successful',
           'data'=>null
        ]);
    }

    public function editLead($id,Request $request)
    {
        $lead = Lead::myRole()->where('id',$id)->first();
        if (empty($lead)){
            return  response()->json([
                'status'=>false,
                'message'=>'Lead not found',
                'data'=>null
            ]);
        }
        $validator = \Validator::make($request->all(),[
            'client_name'=>['required','max:191'],
            'address'=>['required','max:191'],
            'phone'=>['required','max:191'],
            'email'=>['required','max:191'],
            'additional_comments'=>['nullable','max:191'],
            'job_type'=>['required','array']
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
        $lead->client_name = $request->client_name;
        $lead->address = $request->address;
        $lead->phone = $request->phone;
        $lead->email = $request->email;
        $lead->additional_comments = $request->additional_comments;
        if ($lead->save()){
            $jobType = [];
            foreach ($request->job_type as $type){
                if (gettype($type) == 'integer'){
                    $exist = JobType::where('id',$type)->first();
                    if (empty($exist)){
                        continue;
                    }
                    $jobType[] = $exist->id;
                }elseif (gettype($type) == 'string'){
                    $create = JobType::firstOrCreate(['name' => $type]);
                    $jobType[] = $create->id;
                }else{
                    continue;
                }
            }
            $lead->jobTypes()->sync($jobType);
            return  response()->json([
                'status'=>true,
                'message'=>'Lead update successfully',
                'data'=>null
            ]);
        }else{
            return  response()->json([
                'status'=>false,
                'message'=>'Lead not update',
                'data'=>null
            ]);
        }

    }
    public function leadDetails($id)
    {
//       Lead::factory(100)->create()->each(function ($q){
//            $number = JobType::inRandomOrder()->limit(rand(1,JobType::count()))->get()->pluck('id');
//            $q->jobTypes()->sync($number);
//        });

        $lead = Lead::myRole()->with('jobTypes')->where('id',$id)->first();
        if (!empty($lead)){
            $roofTypes = RoofType::where('lead_id',$lead->id)->first();
            $response = [
                'status'=>true,
                'message'=>'',
                'data'=>[
                    'lead'=>$lead,
                    'roofTypes'=>$roofTypes
                ],
            ];
        }else{
            $response = [
                'status'=>false,
                'message'=>'No lead found',
                'data'=>null,
            ];
        }
        return response()->json($response);
    }
}
