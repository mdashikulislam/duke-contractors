<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeckType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeckTypeController extends Controller
{
    public function index()
    {
        return response()->json([
            'status'=>true,
            'message'=>'',
            'data'=>[
                'deckTypes'=>DeckType::all()
            ]
        ]);
    }
    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(),[
            'name'=>['required','max:255','string','unique:deck_types']
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
        $deckType = new DeckType();
        $deckType->name = $request->name;
        if ($deckType->save()){
            $response = [
                'status' => true,
                'message' => 'Deck type added successful',
                'data' => [
                    'deckType'=>$deckType
                ]
            ];
        }else{
            $response = [
                'status' => false,
                'message' => 'Deck type  not added',
                'data' => null
            ];
        }
        return  response()->json($response);
    }
    public function update($id,Request $request)
    {
        $deckType = DeckType::where('id',$id)->first();
        if (empty($deckType)){
            return response()->json([
                'status' => false,
                'message' => 'Deck Type not found',
                'data' => null
            ]);
        }
        $validator = \Validator::make($request->all(),[
            'name'=>['required','max:255','string',Rule::unique('deck_types')->ignore('id')]
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
        $deckType->name = $request->name;
        if ($deckType->save()){
            $response = [
                'status' => true,
                'message' => 'Deck type update successful',
                'data' => [
                    'deckType'=>$deckType
                ]
            ];
        }else{
            $response = [
                'status' => false,
                'message' => 'Deck type not added',
                'data' => null
            ];
        }
        return  response()->json($response);
    }
    public function delete($id, Request $request)
    {
        $deckType = DeckType::where('id',$id)->first();
        if (empty($deckType)){
            return response()->json([
                'status' => false,
                'message' => 'Deck Type not found',
                'data' => null
            ]);
        }
        if ($deckType->delete()){
            $response = [
                'status' => true,
                'message' => 'Deck Type delete successful',
                'data' => null
            ];
        }else{
            $response = [
                'status' => false,
                'message' => 'Deck Type not delete',
                'data' => null
            ];
        }
        return  response()->json($response);
    }
}
