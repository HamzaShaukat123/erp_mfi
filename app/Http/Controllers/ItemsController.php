<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item_entry;
use App\Models\Item_Groups;
use Illuminate\Support\Facades\Validator;

class ItemsController extends Controller
{
    
    public function index()
    {
        $items = Item_entry::where('item_entry.status', 1)
                ->leftjoin('item_group as ig', 'ig.item_group_cod', '=', 'item_entry.item_group')
                ->orderby('it_cod','desc')
                ->get();
        $itemGroups = Item_Groups::where('status', 1)->get();
        return view('items.index',compact('items','itemGroups'));
    }

    public function validation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_name' => 'required|unique:item_entry',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        else{
            return response()->json(['success' => "success"]);
        }
    }

    public function create()
    {
        $item_groups = Item_Groups::where('item_group.status', 1)->get();
        return view('items.create',compact('item_groups'));
    }

    public function store(Request $request)
    {
        if($request->has('items'))
        {
            for($i=0;$i<$request->items;$i++)
            {
                if(filled($request->item_name[$i]))
                {
                    $item_entry = new Item_entry();
                    $item_entry->item_name=$request->item_name[$i];
                    $item_entry->item_group=$request->item_group[$i];
                    $item_entry->item_remark=$request->item_remarks[$i];
                    $item_entry->sales_price=$request->item_s_price[$i];
                    $item_entry->OPP_qty_cost=$request->item_pur_cost[$i];
                    $item_entry->pur_rate_date=$request->purchase_rate_date[$i];
                    $item_entry->sale_rate_date=$request->sale_rate_date[$i];
                    $item_entry->qty=$request->item_stock[$i];
                    $item_entry->opp_qty=$request->item_stock[$i];
                    $item_entry->opp_date=$request->item_date[$i];
                    $item_entry->stock_level=$request->item_stock_level[$i];
                    $item_entry->labourprice=$request->item_l_price[$i];
                    $item_entry->status=1;
                    $item_entry->created_by=session('user_id');

                    $item_entry->save();
                }
            }
        }

        return redirect()->route('all-items');
    }

    public function destroy(Request $request)
    {
        $item_groups = Item_entry::where('it_cod', $request->item_id)->update([
            'status' => '0',
            'updated_by' => session('user_id'),
        ]);
        return redirect()->route('all-items');
    }

    public function update(Request $request)
    {

        $item = Item_entry::where('it_cod', $request->it_cod)->get();
        $item->item_remark=null;

        if ($request->has('item_group') && $request->item_group) {
            $item->item_group=$request->item_group;
        }
        if ($request->has('item_name') && $request->item_name) {
            $item->item_name=$request->item_name;
        }
        if ($request->has('item_remark') && $request->item_remark OR empty($request->item_remark)) {
            $item->item_remark=$request->item_remark;
        }
        if ($request->has('qty') && $request->qty OR $request->qty==0) {
            $item->opp_qty=$request->qty;
        }
        if ($request->has('OPP_qty_cost') && $request->OPP_qty_cost OR $request->OPP_qty_cost==0) {
            $item->OPP_qty_cost=$request->OPP_qty_cost;
        }
        if ($request->has('pur_rate_date') && $request->pur_rate_date) {
            $item->pur_rate_date=$request->pur_rate_date;
        }
        if ($request->has('sales_price') && $request->sales_price OR $request->sales_price==0) {
            $item->sales_price=$request->sales_price;
        }
        if ($request->has('sale_rate_date') && $request->sale_rate_date ) {
            $item->sale_rate_date=$request->sale_rate_date;
        }
        if ($request->has('date') && $request->date) {
            $item->opp_date=$request->date;
        }
        if ($request->has('stock_level') && $request->stock_level OR $request->stock_level==0 ) {
            $item->stock_level=$request->stock_level;
        }
        if ($request->has('labourprice') && $request->labourprice OR $request->labourprice==0) {
            $item->labourprice=$request->labourprice;
        }

        Item_entry::where('it_cod', $request->it_cod)->update([
            'item_name'=>$item->item_name,
            'item_group'=>$item->item_group,
            'item_remark'=>$item->item_remark,
            'opp_qty'=>$item->opp_qty,
            'OPP_qty_cost'=>$item->OPP_qty_cost,
            'pur_rate_date'=>$item->pur_rate_date,
            'sales_price'=>$item->sales_price,
            'sale_rate_date'=>$item->sale_rate_date,
            'opp_date'=>$item->opp_date,
            'stock_level'=>$item->stock_level,
            'labourprice'=>$item->labourprice,
            'updated_by' => session('user_id'),
        ]);
        
        return redirect()->route('all-items');
    }

    public function getItemDetails(Request $request)
    {
        $item_details = Item_entry::where('it_cod', $request->id)->get();
        return $item_details;
    }
    
}
