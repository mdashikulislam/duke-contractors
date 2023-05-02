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
                ->selectRaw("SUM(IF(month = 'Jan', total, 0)) AS 'January',
            SUM(IF(month = 'Feb', total, 0)) AS 'February',
            SUM(IF(month = 'Mar', total, 0)) AS 'March',
            SUM(IF(month = 'Apr', total, 0)) AS 'April',
            SUM(IF(month = 'May', total, 0)) AS 'May',
            SUM(IF(month = 'Jun', total, 0)) AS 'June',
            SUM(IF(month = 'Jul', total, 0)) AS 'July',
            SUM(IF(month = 'Aug', total, 0)) AS 'August',
            SUM(IF(month = 'Sep', total, 0)) AS 'September',
            SUM(IF(month = 'Oct', total, 0)) AS 'October',
            SUM(IF(month = 'Nov', total, 0)) AS 'November',
            SUM(IF(month = 'Dec', total, 0)) AS 'December'")->first();
            $summary[$status] = $monthData;
        }

        $final = [];
        $total = [
            'January'=>0,
            'February'=>0,
            'March'=>0,
            'April'=>0,
            'May'=>0,
            'June'=>0,
            'July'=>0,
            'August'=>0,
            'September'=>0,
            'October'=>0,
            'November'=>0,
            'December'=>0,
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
                ->selectRaw("SUM(IF(month = 'Jan', total, 0)) AS 'January',
            SUM(IF(month = 'Feb', total, 0)) AS 'February',
            SUM(IF(month = 'Mar', total, 0)) AS 'March',
            SUM(IF(month = 'Apr', total, 0)) AS 'April',
            SUM(IF(month = 'May', total, 0)) AS 'May',
            SUM(IF(month = 'Jun', total, 0)) AS 'June',
            SUM(IF(month = 'Jul', total, 0)) AS 'July',
            SUM(IF(month = 'Aug', total, 0)) AS 'August',
            SUM(IF(month = 'Sep', total, 0)) AS 'September',
            SUM(IF(month = 'Oct', total, 0)) AS 'October',
            SUM(IF(month = 'Nov', total, 0)) AS 'November',
            SUM(IF(month = 'Dec', total, 0)) AS 'December'")->first();
            $summary[$type->name] = $monthData;
        }
        $final = [];
        $total = [
            'January'=>0,
            'February'=>0,
            'March'=>0,
            'April'=>0,
            'May'=>0,
            'June'=>0,
            'July'=>0,
            'August'=>0,
            'September'=>0,
            'October'=>0,
            'November'=>0,
            'December'=>0,
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
