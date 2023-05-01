<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
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

    public function edit(Request $request)
    {
        $rules = [
            'id'=>['required','numeric'],
            'name'=>['nullable','max:255','string'],
            'email'=>['nullable','email','string','max:255',Rule::unique('users')->ignore($request->id)],
            'password'=>['nullable', 'string', 'min:8'],
            'role'=>['nullable', 'string', 'in:'.implode(',',ROLE)],
        ];
        $validator = \Validator::make($request->all(),$rules);
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
       $request->except('id');
        if ($request->password){
            $request['password'] = Hash::make($request->password);
        }
       $user = User::where('id',$request->id)->first();
       $user->fill($request->except('id'));
       if ($user->save()){
           $response = [
               'status' => true,
               'message' => 'User update successfully',
               'data' => null
           ];
       }else{
           $response = [
               'status' => false,
               'message' => 'Something wrong',
               'data' => null
           ];
       }
       return response()->json($response);

    }
}
