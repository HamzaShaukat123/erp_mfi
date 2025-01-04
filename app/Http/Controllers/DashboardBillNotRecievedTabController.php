<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\bill_not_recvd;


class DashboardBillNotRecievedTabController extends Controller
{
    public function BillNotRecvd(Request $request)
    {
        $bill_not_recvd = bill_not_recvd::where('bill_not_recvd.account_name', '=', '19')
    ->leftJoin('sales as sales_prefix', 'bill_not_recvd.sale_prefix', '=', 'sales_prefix.prefix')
    ->leftJoin('sales as sales_inv', 'bill_not_recvd.Sal_inv_no', '=', 'sales_inv.Sal_inv_no')
    ->where('bill_not_recvd.remaining_amount', '<>', 0)
    ->groupBy('bill_not_recvd.id') // Replace 'id' with the unique field for `bill_not_recvd`
    ->get();



        return response()->json([
            'bill_not_recvd' => $bill_not_recvd
        ]);
    }
}
