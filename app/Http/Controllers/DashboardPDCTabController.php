<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\dash_pdc_recv;
use App\Models\dash_pdc_pay;

class DashboardPDCTabController extends Controller
{
    public function PDC(Request $request)
    {
        $dash_pdc_recv = dash_pdc_recv::where('ac_dr_sid', '=', 2)
        ->whereNull('voch_id')
        ->orderBy('chqdate', 'asc')
        ->get();

        $dash_pdc_pay = dash_pdc_pay::where('ac_cr_sid', '=', 2)
        ->whereNull('voch_id')
        ->orderBy('chqdate', 'asc')
        ->get();
       

        return response()->json([
            'dash_pdc_recv' => $dash_pdc_recv,
            'dash_pdc_pay' => $dash_pdc_pay
        ]);
    }
}
