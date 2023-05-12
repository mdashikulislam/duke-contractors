<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class MixController extends Controller
{
    public function getCityList()
    {
        return response()->json([
            'status'=>true,
            'message'=>'',
            'data'=>[
                'cities'=>CITY_LIST
            ]
        ]);
    }
}
