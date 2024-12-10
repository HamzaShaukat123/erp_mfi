<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\dash_over_dues;


class DashboardOverDuesTabController extends Controller
{
    public function OverDues(Request $request)
    {
        $dash_over_dues_recv = dash_over_dues::where('sub', '=', 1)
        ->where('over_dues', '>', 0)
        ->orderBy('over_dues', 'desc')
        ->get();


        $dash_over_dues_pay = dash_over_dues::where('sub', '=', 7)
        ->where('over_dues', '<', 0)
        ->orderBy('over_dues', 'desc')
        ->get();

       

        return response()->json([
            'dash_over_dues_recv' => $dash_over_dues_recv,
            'dash_over_dues_pay' => $dash_over_dues_pay
        ]);
    }
}
