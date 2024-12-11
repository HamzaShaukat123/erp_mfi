<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AC;
use App\Models\sale_account_item_group_info;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\myPDF;
use Carbon\Carbon;

class RptItemName1SaleController extends Controller
{
    public function sale(Request $request){
        $sale_account_item_group_info = sale_account_item_group_info::where('item_cod',$request->acc_id)
        ->whereBetween('sa_date', [$request->fromDate, $request->toDate])
        ->orderBy('sa_date', 'asc')
        ->get(['prefix', 'Sal_inv_no','sa_date', 'ac_name', 'weight','qty', 'price']);

        return $sale_account_item_group_info;
    }
}
