<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\bill_not_recvd;


class DashboardBillNotRecievedTabController extends Controller
{
    public function BillNotRecvd(Request $request)
    {
        $bill_not_recvd = bill_not_recvd::select(
            'bill_not_recvd.sale_prefix',
            'bill_not_recvd.Sal_inv_no',
            'bill_not_recvd.bill_date',
            'bill_not_recvd.bill_amount',
            'bill_not_recvd.ttl_jv_amt',
            'bill_not_recvd.remaining_amount',
            'sales.pur_ord_no as sales_pur_ord_no', 
            'sales.Cash_pur_name',
            'tsales.Cash_name',
            'tsales.pur_ord_no as tsales_pur_ord_no'
        )
        ->leftJoin('sales', function($join) {
            $join->on('bill_not_recvd.sale_prefix', '=', 'sales.prefix')
                 ->on('bill_not_recvd.Sal_inv_no', '=', 'sales.Sal_inv_no');
        })
        ->leftJoin('tsales', function($join) {
            $join->on('bill_not_recvd.sale_prefix', '=', 'tsales.prefix')
                 ->on('bill_not_recvd.Sal_inv_no', '=', 'tsales.Sal_inv_no');
        })
        ->where('bill_not_recvd.remaining_amount', '<>', 0)
        ->where('bill_not_recvd.account_name', '=', 19)
        ->get();
    



        return response()->json([
            'bill_not_recvd' => $bill_not_recvd
        ]);
    }
}
