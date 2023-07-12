<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobType;
use App\Models\Lead;
use App\Models\RoofType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\GoogleCalendar\Event;

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
        $leads = Lead::myRole()->with('jobTypes')
            ->whereHas('jobTypes')
            ->with('cityOfPermit')
            ->orderByDesc('created_at')->skip($offset)->limit($limit)->get();
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
            'seller_id'=>['required','numeric'],
            'customer_name'=>['required','max:191'],
            'address'=>['required','max:191'],
            'phone'=>['required','max:191'],
            'email'=>['required','max:191'],
            'additional_comments'=>['nullable','max:191'],
            'job_type'=>['required','array'],
            'city_for_permit'=>['required','exists:\App\Models\City,id'],
            'appointment'=>['required','date_format:Y-m-d H:i:s']
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
        \DB::beginTransaction();
        try {
            $lead = new Lead();
            $lead->user_id = getAuthInfo()->id;
            $lead->customer_name = $request->customer_name;
            $lead->city_for_permit = $request->city_for_permit;
            $lead->seller_id = $request->seller_id;
            $lead->address = $request->address;
            $lead->phone = $request->phone;
            $lead->email = $request->email;
            $lead->additional_comments = $request->additional_comments;
            $lead->price_of_quote = 0;
            $lead->status = 'Not Sent';
            $lead->save();
            $lead->jobTypes()->sync($jobType);
            $description = 'Seller:'.@$lead->sellers->name.'
             Customer Name:'.@$lead->customer_name.'
             Phone:'.@$lead->phone. '
              Email:'.@$lead->email;
            $event = new Event;
            $event->name = 'New Lead '.$lead->id;
            $event->description = $description;
            $event->startDateTime = Carbon::parse($request->appointment)->format('Y-m-d H:i:s');
            $event->endDateTime = Carbon::parse($request->appointment)->addHour()->format('Y-m-d H:i:s');
            $event->save();
            \DB::commit();

            return response()->json([
                'status'=>true,
                'message'=>'Lead added successful',
                'data'=>null
            ]);
        }catch (\Exception $exception){
            \DB::rollBack();
            return response()->json([
                'status'=>false,
                'message'=>$exception->getMessage(),
                'data'=>null
            ]);
        }

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
            'seller_id'=>['required','numeric'],
            'customer_name'=>['required','max:191'],
            'address'=>['required','max:191'],
            'phone'=>['required','max:191'],
            'email'=>['required','max:191'],
            'additional_comments'=>['nullable','max:191'],
            'job_type'=>['required','array'],
            'city_for_permit'=>['required','exists:\App\Models\City,id'],
            'status'=>['required'],
            'estimate_date'=>['nullable','date_format:Y-m-d'],
            'job_completed_date'=>['nullable','date_format:Y-m-d'],
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

        $lead->customer_name = $request->customer_name;
        $lead->city_for_permit = $request->city_for_permit;
        $lead->seller_id = $request->seller_id;
        $lead->address = $request->address;
        $lead->phone = $request->phone;
        $lead->email = $request->email;
        $lead->status = $request->status;
        $lead->additional_comments = $request->additional_comments;
        $lead->job_completed_date = $request->job_completed_date ?? null;
        $lead->estimate_date = $request->estimate_date ?? null;
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
