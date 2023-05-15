<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CityController extends Controller
{
    public function index()
    {
        return response()->json([
            'status'=>true,
            'message'=>'',
            'data'=>[
                'cities'=>City::all()
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'name'=>['required','max:255','string','unique:cities'],
            'tile'=>['required','between:0,999'],
            'metal'=>['required','between:0,999'],
            'shingle'=>['required','between:0,999'],
            'flat'=>['required','between:0,999'],
            'tpo'=>['required','between:0,999']
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
        $city = new City();
        $city->name = $request->name;
        $city->metal = $request->metal;
        $city->shingle = $request->shingle;
        $city->flat = $request->flat;
        $city->tpo = $request->tpo;
        if ($city->save()){
            $response = [
                'status' => true,
                'message' => 'City added successful',
                'data' => [
                    'city'=>$city
                ]
            ];
        }else{
            $response = [
                'status' => false,
                'message' => 'City not added',
                'data' => null
            ];
        }
        return  response()->json($response);
    }

    public function update($id,Request $request)
    {
        $city = City::where('id',$id)->first();
        if (empty($city)){
            return response()->json([
                'status' => false,
                'message' => 'City not found',
                'data' => null
            ]);
        }
        $validator = \Validator::make($request->all(),[
            'name'=>['required','max:255','string',Rule::unique('cities')->ignore('id')],
            'tile'=>['required','between:0,999'],
            'metal'=>['required','between:0,999'],
            'shingle'=>['required','between:0,999'],
            'flat'=>['required','between:0,999'],
            'tpo'=>['required','between:0,999']
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
        $city->name = $request->name;
        $city->metal = $request->metal;
        $city->shingle = $request->shingle;
        $city->flat = $request->flat;
        $city->tpo = $request->tpo;
        if ($city->save()){
            $response = [
                'status' => true,
                'message' => 'City update successful',
                'data' => [
                    'city'=>$city
                ]
            ];
        }else{
            $response = [
                'status' => false,
                'message' => 'City not added',
                'data' => null
            ];
        }
        return  response()->json($response);
    }

    public function delete($id, Request $request)
    {
        $city = City::where('id',$id)->first();
        if (empty($city)){
            return response()->json([
                'status' => false,
                'message' => 'City not found',
                'data' => null
            ]);
        }
        if ($city->delete()){
            $response = [
                'status' => true,
                'message' => 'City delete successful',
                'data' => null
            ];
        }else{
            $response = [
                'status' => false,
                'message' => 'City not delete',
                'data' => null
            ];
        }
        return  response()->json($response);
    }
}
