<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AC;
use App\Models\ac_group;
use App\Models\sub_head_of_acc;
use App\Models\Item_entry2;
use App\Models\Item_entry;
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

    // By Item Name 1
    public function byItemName1()
    {
        $items1 = Item_entry::orderBy('item_name', 'asc')->get();
        return view('reports.item_name1',compact('items1'));
    }

    // By Item Name 2
    public function byItemName2()
    {
        $items2 = Item_entry2::orderBy('item_name', 'asc')->get();
        return view('reports.item_name2',compact('items2'));
    }

    // By Item Group
    public function byItemGroup()
    {
        $items_group = Item_Groups::orderBy('group_name', 'asc')->get();
        return view('reports.item_group',compact('items_group'));
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
        $items = Item_Groups::whereIn('item_group_cod', [1, 2, 3, 4, 5, 6, 7, 8, 10, 11])
        ->orderBy('group_name', 'asc')
        ->get();
        return view('reports.gd_group_name', compact('items'));

    }

    // Daily Register
    public function dailyRegister()
    {
        $coa = AC::orderBy('ac_name', 'asc')->get();
        return view('reports.daily_register',compact('coa'));
    }

    // Commissions
    public function commissions()
    {
        $item_group = Item_Groups::whereBetween('item_group_cod', [1, 6])->get();
        return view('reports.commissions',compact('item_group'));
        
    }
}
