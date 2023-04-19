<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if (isNotAdmin()){
            return response()->json([
                'status'=>false,
                'message'=>'You are not authorize',
                'data'=>null
            ]);
        }
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
        $users = User::select('id','name','email','role')->orderByDesc('created_at')->skip($offset)->limit($limit)->get();
        return response()->json([
           'status'=>true,
           'message'=>'',
           'data'=>[
               'users'=>$users
           ]
        ]);
    }

    public function store(Request $request)
    {
        if (isNotAdmin()){
            return response()->json([
                'status'=>false,
                'message'=>'You are not authorize',
                'data'=>null
            ]);
        }
        $validator = \Validator::make($request->all(),[
            'name'=>['required','max:255','string'],
            'email'=>['required','email','string','max:255','unique:users'],
            'password'=>['required', 'string', 'min:8'],
            'role'=>['required','in:'.implode(',',ROLE)]
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
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->password){
            $user->password = Hash::make($request->password);
        }
        $user->role = $request->role;
        $user->save();
        return response()->json([
            'status'=>true,
            'message'=>'User create successful',
            'data'=>null
        ]);
    }
}
