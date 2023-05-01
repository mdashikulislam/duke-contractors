<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class JobTypeController extends Controller
{
    public function index()
    {
        $types = JobType::all();
        return response()->json([
            'status' => true,
            'message' => '',
            'data' => [
                'types'=>$types
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'name'=>['required','max:255','string','unique:job_types']
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
        $type = new JobType();
        $type->name = $request->name;
        if ($type->save()){
            $response = [
                'status' => true,
                'message' => 'Job type create successful',
                'data' => null
            ];
        }else{
            $response = [
                'status' => false,
                'message' => 'Something went wrong',
                'data' => null
            ];
        }
        return response()->json($response);
    }

    public function edit(Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'job_type_id'=>['required','numeric','exists:\App\Models\JobType,id'],
            'name'=>['required','max:255','string',Rule::unique('job_types')->ignore($request->job_type_id)]
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
        $type = JobType::where('id',$request->job_type_id)->first();
        $type->name = $request->name;
        if ($type->save()){
            $response = [
                'status' => true,
                'message' => 'Job type update successful',
                'data' => null
            ];
        }else{
            $response = [
                'status' => false,
                'message' => 'Something went wrong',
                'data' => null
            ];
        }
        return response()->json($response);
    }
}
