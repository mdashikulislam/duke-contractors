<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerPayment;
use Faker\Provider\Payment;
use Illuminate\Http\Request;

class CustomerPaymentController extends Controller
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
        $payments = CustomerPayment::where('lead_id',$request->lead_id)->orderByDesc('created_at')->get();
        if (empty($payments)){
            return response()->json([
                'status' => false,
                'message' => 'No payment info found',
                'data' => null
            ]);
        }else{
            return response()->json([
                'status' => true,
                'message' => '',
                'data' => [
                    'payments'=>$payments
                ]
            ]);
        }
    }

    public function edit($id,Request $request)
    {
        $paymet = CustomerPayment::where('id',$id)->first();
        if (empty($paymet)){
            return response()->json([
                'status' => false,
                'message' => 'No record found',
                'data' => null
            ]);
        }
        $validator = \Validator::make($request->all(),[
            'lead_id'=>['required','numeric','exists:\App\Models\Lead,id'],
            'amount'=>['required','between:1,99999999999999']
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
        $paymet->lead_id = $request->lead_id;
        $paymet->user_id = auth()->guard('api')->id();
        $paymet->amount = $request->amount;
        if ($paymet->save()){
            return response()->json([
                'status' => true,
                'message' => 'Payment info update successfully',
                'data' => null
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Payment info not update',
                'data' => null
            ]);
        }
    }
    public function create(Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'lead_id'=>['required','numeric','exists:\App\Models\Lead,id'],
            'amount'=>['required','between:1,99999999999999']
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
        $paymet = new CustomerPayment();
        $paymet->lead_id = $request->lead_id;
        $paymet->user_id = auth()->guard('api')->id();
        $paymet->amount = $request->amount;
        if ($paymet->save()){
            return response()->json([
                'status' => true,
                'message' => 'Payment info save successfully',
                'data' => null
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Payment info not saved',
                'data' => null
            ]);
        }
    }

    public function delete(Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'id'=>['required','numeric','exists:\App\Models\CustomerPayment,id']
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
        CustomerPayment::where('id',$request->id)->delete();
        return response()->json([
            'status' => true,
            'message' => 'Record delete successful',
            'data' => null
        ]);

    }
}
