<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'=>['required','max:255','string'],
            'email'=>['required','email','string','max:255','unique:users'],
            'password'=>['required', 'string', 'min:8','confirmed']
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
        \DB::beginTransaction();
        try {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            if ($request->password){
                $user->password = Hash::make($request->password);
            }
            $user->save();
            $token = null;
            if ($user) {
                $token = $user->createToken($user->email)->accessToken;
            }
            \DB::commit();
            $data = [
                'access_token' => $token,
                'access_type' => "Bearer",
                'user_data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'email_verified_at' => $user->email_verified_at,
                    'role'=>$user->role
                ],
            ];
            $response = [
                'status' => true,
                'message' => __('Account is created successfully'),
                'data' => $data
            ];
        }catch (\Exception $exception){
            \DB::rollBack();
            $response = [
                'status' => true,
                'message' => $exception->getMessage(),
                'data' => null
            ];
        }
        return response()->json($response);
    }
    public function login(Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'email'=>['required','string','max:255','email'],
            'password'=>['required','string']
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

        $valid = Auth::attempt($request->only('email','password'));
        if ($valid){
            $user = User::where('id',Auth::id())->first();
            if (empty($user)){
                return [
                    'status' => false,
                    'message' => __("You are not authorized"),
                    'data' => null
                ];
            }
            $token = null;
            if ($user) {
                $token = $user->createToken($user->email)->accessToken;
            }
            $response = [
                'status' => true,
                'message' => __("Successfully signed in"),
                'data' => [
                    'access_token' => $token,
                    'access_type' => "Bearer",
                    'user_data' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'email_verified_at' => $user->email_verified_at,
                        'role'=>$user->role
                    ]
                ]
            ];
            return $response;
        }else{
            return [
                'status' => false,
                'message' => __("These credentials do not match our records."),
                'data' => null
            ];
        }
    }

    public function getCurrentUserInfo()
    {
        $user = Auth::guard('api')->user();
        $response = [
            'status' => true,
            'message' => __("Successfully signed in"),
            'data' => [
                'user_data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'email_verified_at' => $user->email_verified_at,
                    'role'=>$user->role,
                    'setting'=>Setting::first()
                ]
            ]
        ];
        return response()->json($response);
    }
}
