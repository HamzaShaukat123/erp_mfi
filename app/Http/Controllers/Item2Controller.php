<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item_entry2;
use App\Models\Item_Groups;
use Illuminate\Support\Facades\Validator;

class Item2Controller extends Controller
{
    
    public function index()
    {
        $items = Item_entry2::where('item_entry2.status', 1)
                ->leftjoin('item_group as ig', 'ig.item_group_cod', '=', 'item_entry2.item_group')
                ->get();
        $itemGroups = Item_Groups::where('status', 1)->get();

        return view('item2.index',compact('items','itemGroups'));
    }

    public function index1()
    {
        $items = Item_entry2::where('item_entry2.status', 1)
                ->leftjoin('item_group as ig', 'ig.item_group_cod', '=', 'item_entry2.item_group')
                ->get();
        $itemGroups = Item_Groups::where('status', 1)->get();

        return view('item2.index1',compact('items','itemGroups'));
    }

    public function validation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_name' => 'required|unique:item_entry2',
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
        return view('item2.create',compact('item_groups'));
    }

    public function store(Request $request)
    {

        if($request->has('items'))
        {
            for($i=0;$i<$request->items;$i++)
            {
                if(filled($request->item_name[$i]))
                {
                    $Item_entry2 = new Item_entry2();
                    $Item_entry2->item_name=$request->item_name[$i];
                    $Item_entry2->item_group=$request->item_group[$i];
                    $Item_entry2->item_remark=$request->item_remarks[$i];
                    $Item_entry2->sales_price=$request->item_s_price[$i];
                    $Item_entry2->OPP_qty_cost=$request->item_pur_cost[$i];
                    $Item_entry2->pur_rate_date=$request->purchase_rate_date[$i];
                    $Item_entry2->sale_rate_date=$request->sale_rate_date[$i];
                    $Item_entry2->qty=$request->item_stock[$i];
                    $Item_entry2->weight=$request->weight[$i];
                    $Item_entry2->opp_qty=$request->item_stock[$i];
                    $Item_entry2->opp_date=$request->item_date[$i];
                    $Item_entry2->stock_level=$request->item_stock_level[$i];
                    $Item_entry2->labourprice=$request->item_l_price[$i];
                    $Item_entry2->status=1;
                    $Item_entry2->created_by=session('user_id');

                    $Item_entry2->save();
                }
            }
        }

        return redirect()->route('all-items-2');
    }

    public function destroy(Request $request)
    {
        $item_groups = Item_entry2::where('it_cod', $request->item_id)->update(['status' => '0']);
        return redirect()->route('all-items-2');
    }

    public function update(Request $request)
    {
        $item = Item_entry2::where('it_cod', $request->it_cod)->get();
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
        if ($request->has('weight') && $request->weight OR $request->weight==0) {
            $item->weight=$request->weight;
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
        if ($request->has('sale_rate_date') && $request->sale_rate_date) {
            $item->sale_rate_date=$request->sale_rate_date;
        }
        if ($request->has('date') && $request->date) {
            $item->opp_date=$request->date;
        }
        if ($request->has('stock_level') && $request->stock_level OR $request->stock_level==0) {
            $item->stock_level=$request->stock_level;
        }
        if ($request->has('labourprice') && $request->labourprice OR $request->labourprice==0) {
            $item->labourprice=$request->labourprice;
        }

        Item_entry2::where('it_cod', $request->it_cod)->update([
            'item_name'=>$item->item_name,
            'item_group'=>$item->item_group,
            'item_remark'=>$item->item_remark,
            'opp_qty'=>$item->opp_qty,
            'weight'=>$item->weight,
            'OPP_qty_cost'=>$item->OPP_qty_cost,
            'pur_rate_date'=>$item->pur_rate_date,
            'sales_price'=>$item->sales_price,
            'sale_rate_date'=>$item->sale_rate_date,
            'opp_date'=>$item->opp_date,
            'stock_level'=>$item->stock_level,
            'labourprice'=>$item->labourprice,
            'updated_by' => session('user_id'),
        ]);
        
        return redirect()->route('all-items-2');
    }

    public function getItemDetails(Request $request)
    {
        $item_details = Item_entry2::where('it_cod', $request->id)->get();
        return $item_details;
    }
}
