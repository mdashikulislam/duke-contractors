<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobType;
use App\Models\Lead;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SummearyController extends Controller
{
    public function index()
    {
        $summary = [];
        foreach (LEAD_STATUE as $status){
            $monthQuery = Lead::myRole()->selectRaw("MIN(DATE_FORMAT(created_at, '%b')) AS month,
            COUNT(id) AS total")
                ->where('status', $status)
                ->whereNotNull('price_of_quote')
                ->groupByRaw('YEAR(created_at)')
                ->groupByRaw('MONTH(created_at)')
                ->orderByRaw('YEAR(created_at)')
                ->orderByRaw('MONTH(created_at)');
            $monthData =  DB::table($monthQuery)
                ->selectRaw("SUM(IF(month = 'Jan', total, 0)) AS 'Jan',
            SUM(IF(month = 'Feb', total, 0)) AS 'Feb',
            SUM(IF(month = 'Mar', total, 0)) AS 'Mar',
            SUM(IF(month = 'Apr', total, 0)) AS 'Apr',
            SUM(IF(month = 'May', total, 0)) AS 'May',
            SUM(IF(month = 'Jun', total, 0)) AS 'Jun',
            SUM(IF(month = 'Jul', total, 0)) AS 'Jul',
            SUM(IF(month = 'Aug', total, 0)) AS 'Aug',
            SUM(IF(month = 'Sep', total, 0)) AS 'Sep',
            SUM(IF(month = 'Oct', total, 0)) AS 'Oct',
            SUM(IF(month = 'Nov', total, 0)) AS 'Nov',
            SUM(IF(month = 'Dec', total, 0)) AS 'Dec'")->first();
            $summary[$status] = $monthData;
        }

        $final = [];
        $total = [
            'Jan'=>0,
            'Feb'=>0,
            'Mar'=>0,
            'Apr'=>0,
            'May'=>0,
            'Jun'=>0,
            'Jul'=>0,
            'Aug'=>0,
            'Sep'=>0,
            'Oct'=>0,
            'Nov'=>0,
            'Dec'=>0,
        ];
        $totalYtd = 0;
        foreach ($summary as $key => $sum){
            $val = [];
            $ytd = 0;
            foreach ($sum as $k => $s){
                $s = @$s ? intval($s) : 0;
                $val[$k] = $s;
                $total[$k] = $total[$k] + $s;
                $ytd += $s;
                $totalYtd += $s;
            }
            $val['ytd']= $ytd;
            $final[$key] = $val;
        }
        $total['ytd'] = $totalYtd;
        $final['total'] = $total;
        return   response()->json([
            'status'=>true,
            'message'=>'',
            'data'=>[
                'summery'=>$final,
                'calculated_year'=>Carbon::now()->year
            ]
        ]);
    }

    public function jobTypeSalesSummary()
    {
        $summary = [];
        $jobTypes = JobType::all();
        foreach ($jobTypes as $type){
            $monthQuery = Lead::myRole()
                ->whereHas('jobTypes',function ($s) use ($type){
                    $s->where('job_type_id',$type->id);
                })
                ->selectRaw("MIN(DATE_FORMAT(created_at, '%b')) AS month,
            SUM(price_of_quote) AS total")
                //->where('job_type', $type->id)
                ->whereNotNull('price_of_quote')
                ->groupByRaw('YEAR(created_at)')
                ->groupByRaw('MONTH(created_at)')
                ->orderByRaw('YEAR(created_at)')
                ->orderByRaw('MONTH(created_at)');

            $monthData =  DB::table($monthQuery)
                ->selectRaw("SUM(IF(month = 'Jan', total, 0)) AS 'Jan',
            SUM(IF(month = 'Feb', total, 0)) AS 'Feb',
            SUM(IF(month = 'Mar', total, 0)) AS 'Mar',
            SUM(IF(month = 'Apr', total, 0)) AS 'Apr',
            SUM(IF(month = 'May', total, 0)) AS 'May',
            SUM(IF(month = 'Jun', total, 0)) AS 'Jun',
            SUM(IF(month = 'Jul', total, 0)) AS 'Jul',
            SUM(IF(month = 'Aug', total, 0)) AS 'Aug',
            SUM(IF(month = 'Sep', total, 0)) AS 'Sep',
            SUM(IF(month = 'Oct', total, 0)) AS 'Oct',
            SUM(IF(month = 'Nov', total, 0)) AS 'Nov',
            SUM(IF(month = 'Dec', total, 0)) AS 'Dec'")->first();
            $summary[$type->name] = $monthData;
        }
        $final = [];
        $total = [
            'Jan'=>0,
            'Feb'=>0,
            'Mar'=>0,
            'Apr'=>0,
            'May'=>0,
            'Jun'=>0,
            'Jul'=>0,
            'Aug'=>0,
            'Sep'=>0,
            'Oct'=>0,
            'Nov'=>0,
            'Dec'=>0,
        ];
        $totalYtd = 0;
        foreach ($summary as $key => $sum){

            $val = [];
            $ytd = 0;
            foreach ($sum as $k => $s){
                $s = @$s ? intval($s) : 0;
                $val[$k] = $s;
                $total[$k] = $total[$k] + $s;
                $ytd += $s;
                $totalYtd += $s;
            }
            $val['ytd']= $ytd;
            $final[$key] = $val;
        }
        $total['ytd'] = $totalYtd;
        $final['total'] = $total;
        return   response()->json([
            'status'=>true,
            'message'=>'',
            'data'=>[
                'summery'=>$final,
                'calculated_year'=>Carbon::now()->year
            ]
        ]);
    }
}
