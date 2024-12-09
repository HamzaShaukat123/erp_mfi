<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\dash_sale1_not_final;
use App\Models\dash_pur1_not;
use App\Models\dash_sale2_not;
use App\Models\dash_pending_sale_against_pur2;
use App\Models\dash_pending_sale_against_tstock_out;

class DashboardPendingInvoicesTabController extends Controller
{
    public function PENDING_INVOICES(Request $request)
    {
        // Query to fetch pending invoices
        $sale1_not = dash_sale1_not_final::where('bill_not', 0)
        ->get(['prefix', 'Sal_inv_no', 'sa_date','pur_ord_no', 'account_name', 'Cash_pur_name', 'Sales_remarks']);

        $pur1_not = dash_pur1_not::whereNull('sale_against')
        ->get(['prefix', 'pur_id', 'pur_date', 'ac_name', 'cash_saler_name', 'pur_remarks']);

        $sale2_not = dash_sale2_not::whereNull('pur_ord_no')
        ->get(['prefix', 'Sal_inv_no', 'sa_date', 'pur_inv', 'ac_name', 'name_of', 'remarks']);

        $pending_sale_against_pur2 = dash_pending_sale_against_pur2::leftjoin('ac','ac.ac_code','=','dash_pending_sale_against_pur2.account_name')
        ->whereNull('sales_against')
        ->get(['prefix', 'Sale_inv_no', 'sa_date', 'customer_name', 'ac_name', 'Cash_pur_name']);

        $pending_sale_against_tstockout = dash_pending_sale_against_tstock_out::whereNull('sale_against')
        ->get(['prefix', 'Sal_inv_no', 'sa_date', 'cash_pur_name', 'ac_name', 'mill_gate', 'sales_remarks', 'item_type']);


        return response()->json([
            'sale1_not' => $sale1_not,
            'pur1_not' => $pur1_not,
            'sale2_not' => $sale2_not,
            'pending_sale_against_pur2' => $pending_sale_against_pur2,
            'pending_sale_against_tstockout' => $pending_sale_against_tstockout
        ]);
    }
}
