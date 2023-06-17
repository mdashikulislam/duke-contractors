<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CityForPermit;
use App\Models\ContractPrice;
use App\Models\CustomerPayment;
use App\Models\Expense;
use App\Models\InspectionResult;
use App\Models\JobType;
use App\Models\Lead;
use App\Models\RoofingInformation;
use App\Models\RoofType;
use App\Models\SellerCommission;
use App\Models\WoodReplace;
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

        $lead = Lead::with('jobTypes')->whereHas('jobTypes')->with('cityOfPermit')
            ->with('sellers')
            ->where('id',$leadId)->first();
        $roofType = RoofType::where('lead_id',$leadId)->first();
        $customerPayments = CustomerPayment::where('lead_id',$leadId)->get();
        $inspectionResults = InspectionResult::where('lead_id',$leadId)->get();
        $expenses = Expense::where('lead_id',$leadId)->get();

        $sellerCommission = SellerCommission::where('lead_id',$leadId)->get();
        $contractPrice = ContractPrice::where('lead_id',$leadId)->get();
        $roofingInformation = RoofingInformation::where('lead_id',$leadId)->get();
        $woodReplace = WoodReplace::where('lead_id',$leadId)->get();
        $cityForPermit = CityForPermit::where('lead_id',$leadId)->get();
        return response()->json([
            'status' => true,
            'message' => '',
            'data' => [
                'lead'=>$lead,
                'roofType'=>$roofType,
                'customerPayments'=>$customerPayments,
                'inspectionResults'=>$inspectionResults,
                'expenses'=>$expenses,
                'sellerCommission'=>$sellerCommission,
                'contractPrice'=>$contractPrice,
                'roofingInformation'=>$roofingInformation,
                'woodReplace'=>$woodReplace,
                'cityForPermit'=>$cityForPermit,
            ]
        ]);
    }

    public function addUpdate(Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'lead_id'=>['required','numeric','exists:\App\Models\Lead,id'],
            'estimate_date'=>['required','date_format:Y-m-d'],
            'job_completed_date'=>['nullable','date_format:Y-m-d'],
            'permits'=>['nullable','array'],
            'permits.*.amount'=>['required_if:permits,null','between:1,99999999999'],
            'permits.*.company'=>['required_if:permits,null','numeric','exists:\App\Models\OtherCompany,id'],
            'permits.*.status'=>['required_if:permits,null','in:Paid,Pending'],
            'permits.*.date'=>['required_if:permits,null','date_format:Y-m-d'],
            'trash'=>['nullable','array'],
            'trash.*.amount'=>['required_if:trash,null','between:1,99999999999'],
            'trash.*.company'=>['required_if:trash,null','numeric','exists:\App\Models\OtherCompany,id'],
            'trash.*.status'=>['nullable','in:Paid,Pending'],
            'trash.*.date'=>['required_if:trash,null','date_format:Y-m-d'],
            'supplies'=>['nullable','array'],
            'supplies.*.invoice'=>['required_if:supplies,null','max:50'],
            'supplies.*.company'=>['required_if:supplies,null','numeric','exists:\App\Models\OtherCompany,id'],
            'supplies.*.amount'=>['required_if:supplies,null','between:1,99999999999'],
            'supplies.*.status'=>['nullable','in:Paid,Pending'],
            'supplies.*.date'=>['required_if:supplies,null','date_format:Y-m-d'],
            'labour'=>['nullable','array'],
            'labour.*.precio_por_sq'=>['required_if:labour,null','between:1,99999999999'],
            'labour.*.company'=>['required_if:labour,null','numeric','exists:\App\Models\OtherCompany,id'],
            'labour.*.amount'=>['required_if:labour,null','between:1,99999999999'],
            'labour.*.status'=>['nullable','in:Paid,Pending'],
            'labour.*.date'=>['required_if:labour,null','date_format:Y-m-d'],
            'labour.*.deck'=>['required_if:labour,null','string'],
            'customer_payment'=>['nullable','array'],
            'customer_payment.*.amount'=>['required_if:customer_payment,null','between:0,999999999'],
            'customer_payment.*.date'=>['required_if:customer_payment,null','date_format:Y-m-d'],
            'inspection_result'=>['nullable','array'],
            'inspection_result.*.type'=>['required_if:inspection_result,null','max:191'],
            'inspection_result.*.date'=>['required_if:inspection_result,null','date_format:Y-m-d'],
            'seller_commission'=>['nullable','array'],
            'seller_commission.*.amount'=>['required_if:seller_commission,null','numeric','between:0,9999999999'],
            'seller_commission.*.paid'=>['required_if:seller_commission,null','numeric','between:0,9999999999'],
            'seller_commission.*.seller_id'=>['required_if:seller_commission,null','numeric','exists:\App\Models\User,id'],
            'seller_commission.*.status'=>['required_if:seller_commission,null','in:Paid,Pending'],
            'seller_commission.*.date'=>['required_if:seller_commission,null','date_format:Y-m-d'],
            'contract_price'=>['nullable','array'],
            'contract_price.*.label'=>['required_if:contract_price,null','string'],
            'contract_price.*.value'=>['required_if:contract_price,null','between:0,9999999999'],
            'contract_price.*.percent'=>['required_if:contract_price,null','between:0,9999999999'],
            'roofing_information'=>['nullable','array'],
            'roofing_information.*.deck'=>['required_if:roofing_information,null','string'],
            'roofing_information.*.perimeter'=>['required_if:roofing_information,null','between:0,999999999'],
            'roofing_information.*.area'=>['required_if:roofing_information,null','between:0,999999999'],
            'roofing_information.*.pitch'=>['required_if:roofing_information,null','string'],
            'wood_replace'=>['nullable','array'],
            'wood_replace.*.description'=>['required_if:wood_replace,null','string'],
            'wood_replace.*.measure'=>['nullable','string'],
            'wood_replace.*.unit'=>['required_if:wood_replace,null','between:0,999999999'],
            'wood_replace.*.quantity'=>['required_if:wood_replace,null','between:0,999999999'],
            'wood_replace.*.total'=>['required_if:wood_replace,null','between:0,999999999'],
            'wood_replace.*.discount'=>['required_if:wood_replace,null','between:0,999999999'],
            'wood_replace.*.collect'=>['required_if:wood_replace,null','between:0,999999999'],
            'city_for_permit'=>['nullable','array'],
            'city_for_permit.*.stage'=>['required_if:city_for_permit,null','string'],
            'city_for_permit.*.date'=>['required_if:city_for_permit,null','date_format:Y-m-d'],
            'city_for_permit.*.comment'=>['required_if:city_for_permit,null','string'],
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
//        $jobType = [];
//        if (!empty($request->job_type)){
//            foreach ($request->job_type as $type){
//                if (gettype($type) == 'integer'){
//                    $exist = JobType::where('id',$type)->first();
//                    if (empty($exist)){
//                        continue;
//                    }
//                    $jobType[] = $exist->id;
//                }elseif (gettype($type) == 'string'){
//                    $create = JobType::firstOrCreate(['name' => $type]);
//                    $jobType[] = $create->id;
//                }else{
//                    continue;
//                }
//            }
//            $lead->jobTypes()->sync($jobType);
//        }
        Expense::where('type','Permits')->where('lead_id',$leadId)->delete();
        if (!empty($request->permits)){
            foreach ($request->permits as $permit){
                $expense = new Expense();
                $expense->lead_id = $leadId;
                $expense->type = 'Permits';
                $expense->amount = @$permit['amount'];
                $expense->company_id = @$permit['company'];
                $expense->description = @$permit['description'];
                $expense->status = ucfirst(@$permit['status']);
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
                $expense->status = ucfirst(@$trash['status']);
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
                $expense->status = ucfirst(@$supplies['status']);
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
                $expense->status = ucfirst(@$labour['status']);
                $expense->date = @$labour['date'];
                $expense->save();
            }
        }
        $userId = auth()->guard('api')->id();
        CustomerPayment::where('lead_id',$leadId)->delete();
        if (!empty($request->customer_payment)){
            foreach ($request->customer_payment as $payment){
                $pay = new CustomerPayment();
                $pay->lead_id = $leadId;
                $pay->user_id = $userId;
                $pay->amount = @$payment['amount'];
                $pay->date = @$payment['date'];
                $pay->save();
            }
        }
        InspectionResult::where('lead_id',$leadId)->delete();
        if (!empty($request->inspection_result)){
            foreach ($request->inspection_result as $inspectionResult){
                $result = new InspectionResult();
                $result->lead_id = $leadId;
                $result->user_id = $userId;
                $result->type = @$inspectionResult['type'];
                $result->status = ucfirst(@$inspectionResult['status']);
                $result->date = @$inspectionResult['date'];
                $result->save();
            }
        }
        SellerCommission::where('lead_id',$leadId)->delete();
        if (!empty($request->seller_commission)){
            foreach ($request->seller_commission as $commision){
                $result = new SellerCommission();
                $result->lead_id = $leadId;
                $result->seller_id = $commision['seller_id'];
                $result->amount = @$commision['amount'];
                $result->paid = @$commision['paid'];
                $result->date = @$commision['date'];
                $result->status = ucfirst(@$commision['status']);
                $result->save();
            }
        }
        ContractPrice::where('lead_id',$leadId)->delete();
        if (!empty($request->contract_price)){
            foreach ($request->contract_price as $price){
                $result = new ContractPrice();
                $result->lead_id = $leadId;
                $result->label = $price['label'];
                $result->value = @$price['value'];
                $result->percent = @$price['percent'];
                $result->save();
            }
        }
        RoofingInformation::where('lead_id',$leadId)->delete();
        if (!empty($request->roofing_information)){
            foreach ($request->roofing_information as $information){
                $result = new RoofingInformation();
                $result->lead_id = $leadId;
                $result->deck = @$information['deck'];
                $result->perimeter = @$information['perimeter'];
                $result->area = @$information['area'];
                $result->pitch = @$information['pitch'];
                $result->save();
            }
        }
        WoodReplace::where('lead_id',$leadId)->delete();
        if (!empty($request->wood_replace)){
            foreach ($request->wood_replace as $wood){
                $result = new WoodReplace();
                $result->lead_id = $leadId;
                $result->description = @$wood['description'];
                $result->measure = @$wood['measure'];
                $result->unit = @$wood['unit'];
                $result->quantity = @$wood['quantity'];
                $result->total = @$wood['total'];
                $result->discount = @$wood['discount'];
                $result->collect = @$wood['collect'];
                $result->save();
            }
        }
        CityForPermit::where('lead_id',$leadId)->delete();
        if (!empty($request->city_for_permit)){
            foreach ($request->city_for_permit as $permit){
                $result = new CityForPermit();
                $result->lead_id = $leadId;
                $result->stage = @$permit['stage'];
                $result->date = @$permit['date'];
                $result->comment = @$permit['comment'];
                $result->save();
            }
        }
        return response()->json([
            'status' => true,
            'message' => 'Update successful',
            'data' => null
        ]);
    }

    public function getSupplierList(Request $request)
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
        $sellers = Expense::selectRaw('expenses.*,leads.customer_name,leads.address,leads.email,leads.phone')
            ->join('leads','leads.id','=','expenses.lead_id')
            ->with('otherCompanies')
            ->whereHas('otherCompanies')
            ->where('expenses.type','Supplies')
            ->orderByDesc('expenses.created_at')
            ->skip($offset)->limit($limit)
            ->get();
        return response()->json([
            'status' => true,
            'message' => '',
            'data' => [
                'sellers'=>$sellers
            ]
        ]);

    }
}
