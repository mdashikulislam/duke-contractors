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
            'name'=>['required','max:255','string','unique:companies,name'],
            'is_default'=>['required','numeric','between:0,1']
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
        $company->is_default = $request->is_default;
        if ($company->save()){
            if ($request->is_default == 1){
                Company::where('id','!=',$company->id)->update([
                   'is_default' => 0
                ]);
            }
            $response = [
                'status' => true,
                'message' => 'Company add successful',
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
            'name'=>['required','max:255','string',Rule::unique('companies')->ignore($id)],
            'is_default'=>['required','numeric','between:0,1']
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
        $company->is_default = $request->is_default;
        if ($company->save()){
            if ($request->is_default == 1){
                Company::where('id','!=',$company->id)->update([
                    'is_default' => 0
                ]);
            }
            $response = [
                'status' => true,
                'message' => 'Company update successful',
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

    public function delete($id)
    {
        $company  = Company::where('id',$id)->first();
        if (empty($company)){
            return response()->json([
                'status' => false,
                'message' => 'Company not found',
                'data' => null
            ]);
        }
        $company->delete();
        return response()->json([
            'status' => true,
            'message' => 'Company delete successful',
            'data' => null
        ]);
    }
}
