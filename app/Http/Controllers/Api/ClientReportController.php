<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerPayment;
use App\Models\Expense;
use App\Models\InspectionResult;
use App\Models\JobType;
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
            'estimate_date'=>['required','date_format:Y-m-d'],
            'job_completed_date'=>['required','date_format:Y-m-d'],
            'job_type'=>['required','array'],
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
        $leadId = $request->lead_id;
        $lead = Lead::where('id',$leadId)->first();
        $lead->estimate_date = $request->estimate_date;
        $lead->job_completed_date = $request->job_completed_date;
        $lead->save();
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
        Expense::where('type','Permits')->where('lead_id',$leadId)->delete();
        if (!empty($request->permits)){
            foreach ($request->permits as $permit){
                $expense = new Expense();
                $expense->lead_id = $leadId;
                $expense->type = 'Permits';
                $expense->amount = @$permit['amount'];
                $expense->company_id = @$permit['company'];
                $expense->description = @$permit['description'];
                $expense->status = @$permit['status'];
                $expense->date = @$permit['date'];
                $expense->save();
            }
        }
        Expense::where('type','Trash')->where('lead_id',$leadId)->delete();
        if (!empty($request->trash)){
            foreach ($request->trash as $trash){
                $expense = new Expense();
                $expense->lead_id = $leadId;
                $expense->type = 'Trash';
                $expense->amount = @$trash['amount'];
                $expense->company_id = @$trash['company'];
                $expense->description = @$trash['description'];
                $expense->status = @$trash['status'];
                $expense->date = @$trash['date'];
                $expense->save();
            }
        }
        Expense::where('type','Supplies')->where('lead_id',$leadId)->delete();
        if (!empty($request->supplies)){
            foreach ($request->supplies as $supplies){
                $expense = new Expense();
                $expense->lead_id = $leadId;
                $expense->type = 'Supplies';
                $expense->invoice = @$supplies['invoice'];
                $expense->company_id = @$supplies['company'];
                $expense->amount = @$supplies['amount'];
                $expense->description = @$supplies['description'];
                $expense->status = @$supplies['status'];
                $expense->date = @$supplies['date'];
                $expense->save();
            }
        }

        Expense::where('type','Labour')->where('lead_id',$leadId)->delete();
        if (!empty($request->labour)){
            foreach ($request->labour as $labour){
                $expense = new Expense();
                $expense->lead_id = $leadId;
                $expense->type = 'Labour';
                $expense->amount = @$labour['amount'];
                $expense->precio_por_sq = @$labour['precio_por_sq'];
                $expense->company_id = @$labour['company'];
                $expense->description = @$labour['description'];
                $expense->deck = @$labour['deck'];
                $expense->status = @$labour['status'];
                $expense->date = @$labour['date'];
                $expense->save();
            }
        }
        return response()->json([
            'status' => true,
            'message' => 'Update successful',
            'data' => null
        ]);
    }
}
