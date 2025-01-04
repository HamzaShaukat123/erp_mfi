<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\bill_not_recvd;


class DashboardBillNotRecievedTabController extends Controller
{
    public function BillNotRecvd(Request $request)
    {
        $bill_not_recvd = bill_not_recvd::where('account_name', '=', '19')
        ->where('remaining_amount', '<>', 0)
        ->get();



        return response()->json([
            'bill_not_recvd' => $bill_not_recvd
        ]);
    }
}
