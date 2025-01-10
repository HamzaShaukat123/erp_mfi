<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\sales_ageing;
use App\Models\purchase_ageing;
use App\Models\unadjusted_sales_ageing_jv2;
use App\Models\unadjusted_purchase_ageing_jv2;

class DashboardUnAdjustedVouchersTabController extends Controller
{
    public function UV(Request $request)
    {
        $sales_ageing = sales_ageing::leftJoin('ac', 'ac.ac_code', '=', 'sales_ageing.acc_name')
            ->where('sales_ageing.status', 0)
            ->get(['jv2_id', 'sales_prefix', 'sales_id', 'ac_name', 'amount']);

        $purchase_ageing = purchase_ageing::leftJoin('ac', 'ac.ac_code', '=', 'purchase_ageing.acc_name') // Corrected the table name here
            ->where('purchase_ageing.status', 0)
            ->get(['jv2_id', 'sales_prefix', 'sales_id', 'ac_name', 'amount']);


        $unadjusted_sales_ageing_jv2 = unadjusted_sales_ageing_jv2::where('unadjusted_sales_ageing_jv2.remaining_amount', '!=', 0)
            ->where('unadjusted_sales_ageing_jv2.SumCredit', '!=', 0)
            ->orderBy('jv_date')
            ->get(['jv2_id', 'prefix','ac_name', 'SumCredit','jv_date','pur_age_amount','remaining_amount']);

        $unadjusted_purchase_ageing_jv2 = unadjusted_purchase_ageing_jv2::where('unadjusted_purchase_ageing_jv2.remaining_amount', '!=', 0)
            ->whereNotIn('unadjusted_purchase_ageing_jv2.account_cod', [25, 11])
            ->where('unadjusted_purchase_ageing_jv2.SumDebit', '!=', 0)
            ->orderBy('jv_date')
            ->get(['jv2_id', 'prefix','ac_name', 'SumDebit','jv_date','pur_age_amount','remaining_amount']);



        return response()->json([
            'sales_ageing' => $sales_ageing,
            'purchase_ageing' => $purchase_ageing,
            'unadjusted_sales_ageing_jv2' => $unadjusted_sales_ageing_jv2,
            'unadjusted_purchase_ageing_jv2' => $unadjusted_purchase_ageing_jv2
        ]);
    }

}
