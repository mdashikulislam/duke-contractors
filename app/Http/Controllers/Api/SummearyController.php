<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;

class SummearyController extends Controller
{
    public function index()
    {
        $summary = [];
        //Cooking
        $cookingMonthQuery = Lead::myRole()->selectRaw("MIN(DATE_FORMAT(created_at, '%b')) AS month,
            SUM(price_of_quote) AS total")
            ->where('status', 'Cooking')
            ->whereNotNull('price_of_quote')
            ->groupByRaw('YEAR(created_at)')
            ->groupByRaw('MONTH(created_at)')
            ->orderByRaw('YEAR(created_at)')
            ->orderByRaw('MONTH(created_at)');
        $cookingMonthData =  \DB::table($cookingMonthQuery)
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
        //Dead deal
        $deadDealMonthQuery = Lead::myRole()
            ->selectRaw("MIN(DATE_FORMAT(created_at, '%b')) AS month,SUM(price_of_quote) AS total")
            ->where('status', 'Dead Deal')
            ->whereNotNull('price_of_quote')
            ->groupByRaw('YEAR(created_at)')
            ->groupByRaw('MONTH(created_at)')
            ->orderByRaw('YEAR(created_at)')
            ->orderByRaw('MONTH(created_at)');
        $deadDealMonthData =  \DB::table($deadDealMonthQuery)
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

        $approvedMonthQuery = Lead::myRole()
            ->selectRaw("MIN(DATE_FORMAT(created_at, '%b')) AS month,SUM(price_of_quote) AS total")
            ->where('status', 'Approved')
            ->whereNotNull('price_of_quote')
            ->groupByRaw('YEAR(created_at)')
            ->groupByRaw('MONTH(created_at)')
            ->orderByRaw('YEAR(created_at)')
            ->orderByRaw('MONTH(created_at)');
        $approvedMonthData =  \DB::table($approvedMonthQuery)
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

        $sentMonthQuery = Lead::myRole()
            ->selectRaw("MIN(DATE_FORMAT(created_at, '%b')) AS month,SUM(price_of_quote) AS total")
            ->where('status', 'Sent')
            ->whereNotNull('price_of_quote')
            ->groupByRaw('YEAR(created_at)')
            ->groupByRaw('MONTH(created_at)')
            ->orderByRaw('YEAR(created_at)')
            ->orderByRaw('MONTH(created_at)');
        $sentMonthData =  \DB::table($sentMonthQuery)
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

        $notSentMonthQuery = Lead::myRole()
            ->selectRaw("MIN(DATE_FORMAT(created_at, '%b')) AS month,SUM(price_of_quote) AS total")
            ->where('status', 'Not Sent')
            ->whereNotNull('price_of_quote')
            ->groupByRaw('YEAR(created_at)')
            ->groupByRaw('MONTH(created_at)')
            ->orderByRaw('YEAR(created_at)')
            ->orderByRaw('MONTH(created_at)');
        $notSentMonthData =  \DB::table($notSentMonthQuery)
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


        $summary['Cooking']         = $cookingMonthData;
        $summary['Dead Deal']       = $deadDealMonthData;
        $summary['Approved']        = $approvedMonthData;
        $summary['Sent']            = $sentMonthData;
        $summary['Not Sent']        = $notSentMonthData;

        return   $summary;
    }
}
