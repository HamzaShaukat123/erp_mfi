<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AC;
use App\Models\ac_group;
use App\Models\sub_head_of_acc;
use App\Models\Item_entry2;
use App\Models\Item_Groups;
use App\Models\pur_by_account;
use App\Models\pipe_pur_by_account;
use App\Exports\Purchase1Export;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\myPDF;
use Carbon\Carbon;

class ReportingController extends Controller
{
    // By Account Name
    public function byAccountName()
    {
        $coa = AC::orderBy('ac_name', 'asc')->get();
        return view('reports.acc_name',compact('coa'));
    }

    // By Account Group
    public function byAccountGroup()
    {
        $ac_group = ac_group::orderBy('group_name', 'asc')->get();
        $sub_head_of_acc = sub_head_of_acc::orderBy('sub', 'asc')->get();
        return view('reports.acc_group',compact('ac_group','sub_head_of_acc'));
    }

    // By Godown Item Name
    public function byGodownItemName()
    {
        $items = Item_entry2::orderBy('item_name', 'asc')->get();
        return view('reports.gd_item_name',compact('items'));
    }

    // By Godown Group Name
    public function byGodownGroupName()
    {
        $items = Item_Groups::orderBy('group_name', 'asc')->get();
        return view('reports.gd_group_name',compact('items'));
    }

    // Daily Register
    public function dailyRegister()
    {
        $coa = AC::orderBy('ac_name', 'asc')->get();
        return view('reports.daily_register',compact('coa'));
    }

    public function commissions()
    {
        $item_group = Item_Groups::orderBy('group_name', 'asc')->get();
        return view('reports.commissions',compact('item_group'));
    }
}
