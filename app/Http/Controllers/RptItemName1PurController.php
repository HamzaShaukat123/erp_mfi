<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AC;
use App\Models\pur_account_item_group_info;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\myPDF;
use Carbon\Carbon;
class RptItemName1PurController extends Controller
{
    public function pur_account_item_group_info(Request $request){
        $pur_account_item_group_info = pur_account_item_group_info::where('item_cod',$request->acc_id)
        ->whereBetween('pur_date', [$request->fromDate, $request->toDate])
        ->orderBy('pur_date', 'asc')
        ->get(['prefix', 'pur_id','pur_date', 'ac_name', 'weight','qty', 'price']);

        return $pur_account_item_group_info;
    }
}
