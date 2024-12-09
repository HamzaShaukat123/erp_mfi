<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\annual_sale;
use App\Models\annual_pur;

class DashboardANNUALTabController extends Controller
{
    public function ANNUAL(Request $request)
    {
        $fromMonth = $request->from;
        $toMonth = $request->to;
        
        // Sum ttl_weight and total_dr_amt, group by sale_type
        $annual_sale = annual_sale::whereBetween('dat', [$fromMonth, $toMonth])
            ->groupBy('sale_type')
            ->selectRaw('sale_type, SUM(ttl_weight) as total_weight, SUM(total_dr_amt) as total_dr_amount')
            ->get();
        
        // Sum ttl_weight and total_cr_amt, group by pur_type
        $annual_pur = annual_pur::whereBetween('dat', [$fromMonth, $toMonth])
            ->groupBy('pur_type')
            ->selectRaw('pur_type, SUM(ttl_weight) as total_weight, SUM(total_cr_amt) as total_cr_amount')
            ->get();
        
        return response()->json([
            'annual_sale' => $annual_sale,
            'annual_pur' => $annual_pur
        ]);
        
    }

}
