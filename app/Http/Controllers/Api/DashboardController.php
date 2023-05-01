<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobType;
use App\Models\Lead;
use Illuminate\Support\Facades\DB;

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

    public function jobTypePieChart()
    {

        $typeQuery = Lead::myRole()->selectRaw(" job_type,COUNT(id) AS total")
            ->whereNotNull('price_of_quote')
            ->groupBy('job_type');
        $jobTypes = JobType::all();
        $typeResult = DB::table($typeQuery);
                $select = "";
                foreach ($jobTypes as $key => $type){
                    $select .="SUM(IF(job_type = '$type->id', total, 0)) AS '$type->name'";
                    if ($key < count($jobTypes) - 1 ){
                        $select .=",";
                    }
                }
        $typeResult =  $typeResult->selectRaw($select)->first();
        $total = array_sum((array)$typeResult);

        $finalResult = [];
        foreach ($typeResult as $key => $result){
            if (is_null($result) || $result == 0){
                $finalResult[$key] = 0;
            }else{
                $finalResult[$key] = number_format((intval($result) / $total) * 100,2);
            }

        }
        return response()->json([
            'status'=>true,
            'message'=>'',
            'data'=>[
                'pie'=>$finalResult,
                'total'=>$total,
                'symbol'=>'%'
            ]
        ]);
    }
}
