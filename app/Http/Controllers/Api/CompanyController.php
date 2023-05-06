<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\JobType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    public function index()
    {

        $companies = Company::orderByDesc('created_at')->get();
        return response()->json([
            'status' => true,
            'message' => '',
            'data' => [
                'companies'=>$companies
            ]
        ]);


    }

    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'name'=>['required','max:255','string','unique:companies,name']
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
        $company = new Company();
        $company->name = $request->name;
        if ($company->save()){
            $response = [
                'status' => true,
                'message' => '',
                'data' => [
                    'company'=>$company
                ]
            ];
        }else{
            $response = [
                'status' => false,
                'message' => 'Company not create',
                'data' => null
            ];
        }
        return response()->json($response);
    }

    public function edit($id,Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'name'=>['required','max:255','string',Rule::unique('companies')->ignore('id')]
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
        $company  = Company::where('id',$id)->first();
        if (empty($company)){
            return response()->json([
               'status'=>false,
               'message'=>'Company not found',
               'data'=>null
            ]);
        }
        $company->name = $request->name;
        if ($company->save()){
            $response = [
                'status' => true,
                'message' => '',
                'data' => [
                    'company'=>$company
                ]
            ];
        }else{
            $response = [
                'status' => false,
                'message' => 'Company not create',
                'data' => null
            ];
        }
        return response()->json($response);
    }
}
