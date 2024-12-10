<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\dash_over_days_sales;


class DashboardOverDaysTabController extends Controller
{
    public function OverDays(Request $request)
    {
        $dash_over_days_sales = dash_over_days_sales::where('remaining_amount', '!=', 0)
         ->get();


       

        return response()->json([
            'dash_over_days_sales' => $dash_over_days_sales
        ]);
    }
}
