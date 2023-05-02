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
        $approvedLead = Lead::myRole()->where('status', 'Approved')->count();
        $deadDeal = Lead::myRole()->where('status', 'Dead Deal')->count();
        $totalSales = Lead::myRole()->whereNotNull('price_of_quote')->sum('price_of_quote');

        return response()->json([
            'status' => true,
            'message' => '',
            'data' => [
                'totalLead' => $totalLead,
                'approvedLead' => $approvedLead,
                'deadDeal' => $deadDeal,
                'totalSales' => $totalSales
            ]
        ]);
    }

    public function jobTypePieChart()
    {

        $typeQuery = Lead::myRole()->selectRaw("job_types.id as job_type, COUNT(leads.id) AS total")
            ->join('lead_job_types', 'lead_job_types.lead_id', '=', 'leads.id')
            ->join('job_types', 'job_types.id', '=', 'lead_job_types.job_type_id')
            ->whereNotNull('leads.price_of_quote')
            ->groupBy('job_types.id');
        $jobTypes = JobType::all();
        $typeResult = DB::table($typeQuery);
        $select = "";
        foreach ($jobTypes as $key => $type) {
            $select .= "SUM(IF(job_type = '$type->id', total, 0)) AS '$type->name'";
            if ($key < count($jobTypes) - 1) {
                $select .= ",";
            }
        }
        $typeResult = $typeResult->selectRaw($select)->first();
        $total = array_sum((array)$typeResult);

        $finalResult = [];
        foreach ($typeResult as $key => $result) {
            if (is_null($result) || $result == 0) {
                $finalResult[$key] = 0;
            } else {
                $finalResult[$key] = number_format((intval($result) / $total) * 100, 2);
            }
        }
        return response()->json([
            'status' => true,
            'message' => '',
            'data' => [
                'pie' => $finalResult,
                'total' => $total,
                'symbol' => '%'
            ]
        ]);
    }

    public function salesBarChart()
    {
        $monthQuery = Lead::myRole()
            ->selectRaw("MIN(DATE_FORMAT(created_at, '%b')) AS month,
            SUM(price_of_quote) AS total")
            ->whereNotNull('price_of_quote')
            ->groupByRaw('YEAR(created_at)')
            ->groupByRaw('MONTH(created_at)')
            ->orderByRaw('YEAR(created_at)')
            ->orderByRaw('MONTH(created_at)');

        $monthData = DB::table($monthQuery)
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
        return $monthData;
    }
}
