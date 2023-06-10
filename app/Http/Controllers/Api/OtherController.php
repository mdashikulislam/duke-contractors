<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\OtherCompany;
use Illuminate\Http\Request;

class OtherController extends Controller
{
    public function index(Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'type'=>['nullable','in:'.implode(',',OTHER_COMPANY_TYPE)]
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
        $companies = OtherCompany::whereNotNull('type');
        if ($request->type){
            $companies = $companies->where('type',$request->type);
        }
        $companies = $companies->orderByDesc('created_at')->get();
        return response()->json([
            'status' => true,
            'message' => '',
            'data' => [
                'companies'=>$companies
            ]
        ]);
    }

    public function create(Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'name'=>['required','max:191'],
            'type'=>['required','in:'.implode(',',OTHER_COMPANY_TYPE)]
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
        $company = new OtherCompany();
        $company->name = $request->name;
        $company->type = $request->type;
        if ($company->save()){
            return response()->json([
                'status' => true,
                'message' => 'Other company added successful',
                'data' => null
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Other company not added',
                'data' => null
            ]);
        }
    }

    public function update($id,Request $request)
    {
        $company = OtherCompany::where('id',$id)->first();
        if (empty($company)){
            return response()->json([
                'status' => false,
                'message' => 'Other company not found',
                'data' => null
            ]);
        }
        $validator = \Validator::make($request->all(),[
            'name'=>['required','max:191'],
            'type'=>['required','in:'.implode(',',OTHER_COMPANY_TYPE)]
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
        $company->name = $request->name;
        $company->type = $request->type;
        if ($company->save()){
            return response()->json([
                'status' => true,
                'message' => 'Other company update successful',
                'data' => null
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Other company not update',
                'data' => null
            ]);
        }
    }

    public function delete(Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'id'=>['required','numeric','exists:\App\Models\OtherCompany,id'],
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
        OtherCompany::where('id',$request->id)->delete();
        return response()->json([
            'status' => true,
            'message' => 'Other company delete successful',
            'data' => null
        ]);
    }
}
