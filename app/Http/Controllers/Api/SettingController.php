<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {

    }

    public function update(Request $request)
    {

        $validator = \Validator::make($request->all(),[
            'tax'=>['required','between:0,99']
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
        $setting = Setting::first();
        if (empty($setting)){
            $setting = new Setting();
        }
        $setting->tax = $request->tax;
        $setting->save();
        return response()->json([
            'status' => true,
            'message' => 'Setting update successful',
            'data' => null
        ]);
    }
}
