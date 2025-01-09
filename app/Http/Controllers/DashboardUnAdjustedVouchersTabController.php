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

        $unadjusted_sales_ageing_jv2 = unadjusted_sales_ageing_jv2::where('unadjusted_sales_ageing_jv2.AccountType', 1)
            ->get(['jv2_id', 'prefix', 'auto_lager', 'ac_name', 'Credit','jv_date']);

        $unadjusted_purchase_ageing_jv2 = unadjusted_purchase_ageing_jv2::where('unadjusted_purchase_ageing_jv2.AccountType', 7)
            ->get(['jv2_id', 'prefix', 'auto_lager', 'ac_name', 'Debit','jv_date']);

        return response()->json([
            'sales_ageing' => $sales_ageing,
            'purchase_ageing' => $purchase_ageing,
            'unadjusted_sales_ageing_jv2' => $unadjusted_sales_ageing_jv2,
            'unadjusted_purchase_ageing_jv2' => $unadjusted_purchase_ageing_jv2
        ]);
    }

}
