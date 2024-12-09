<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\dash_item_of_month_by_weight_pur2;
use App\Models\dash_item_of_month_by_qty_pur2;

class DashboardItemOfTheMonthTabController extends Controller
{
    public function ItemOfMonth(Request $request)
    {
        $month = $request->month;

        $itemGroups = [
            'hr' => 7,
            'wt' => 8,
            'crc' => 1,
            'eco' => 5,
            'cosmo' => 6
        ];

        $data = [];
        foreach ($itemGroups as $key => $group) {
            $data["{$key}byweight"] = dash_item_of_month_by_weight_pur2::where('dat', $month)
                ->where('item_group_code', $group)
                ->get(['item_name', 'weight']);

            $data["{$key}byqty"] = dash_item_of_month_by_qty_pur2::where('dat', $month)
                ->where('item_group_code', $group)
                ->get(['item_name', 'qty']);
        }

        return response()->json($data);
    }
}
