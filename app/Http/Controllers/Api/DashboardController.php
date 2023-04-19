<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;

class DashboardController extends Controller
{
    public function index()
    {
        $totalLead = Lead::myRole()->count();
        $approvedLead = Lead::myRole()->where('status','Approved')->count();
        $deadDeal = Lead::myRole()->where('status','Dead Deal')->count();
        $totalSales = Lead::myRole()->whereNotNull('price_of_quote')->sum('price_of_quote');
        return response()->json([
            'status'=>true,
            'message'=>'',
            'data'=>[
                'totalLead'=>$totalLead,
                'approvedLead'=>$approvedLead,
                'deadDeal'=>$deadDeal,
                'totalSales'=>$totalSales
            ]
        ]);
    }
}
