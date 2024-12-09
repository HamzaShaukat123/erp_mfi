<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item_Groups;

class ItemGroupsController extends Controller
{
    public function index()
    {
        $itemGroups = Item_Groups::where('status', 1)->get();
        return view('item_groups.index',compact('itemGroups'));
    }


    public function store(Request $request)
    {
        $item_group = new Item_Groups();
        $item_group->created_by = session('user_id');

        if ($request->has('group_name') && $request->group_name) {
            $item_group->group_name=$request->group_name;
        }
        if ($request->has('group_remarks') && $request->group_remarks OR empty($request->group_remarks)) {
            $item_group->group_remarks=$request->group_remarks;
        }
        $item_group->save();
        return redirect()->route('all-item-groups');
    }

    public function destroy(Request $request)
    {
        $item_group = Item_Groups::where('item_group_cod', $request->item_group_cod)->update([
            'status' => '0',
            'updated_by' => session('user_id')
        ]);
        return redirect()->route('all-item-groups');
    }

    public function update(Request $request)
    {
        $group_name= null;
        $group_remarks=null;
       
        if ($request->has('group_name') && $request->group_name) {
            $group_name=$request->group_name;
        }
        if ($request->has('group_remarks') && $request->group_remarks) {
            $group_remarks=$request->group_remarks;
        }
       
        Item_Groups::where('item_group_cod', $request->item_group_cod)->update([
            'group_name'=>$group_name,
            'group_remarks'=>$group_remarks,
            'updated_by' => session('user_id'),
        ]);
        
        return redirect()->route('all-item-groups');
    }

    public function getGroupDetails(Request $request)
    {
        $item_group_details = Item_Groups::where('item_group_cod', $request->id)->get()->first();
        return $item_group_details;
    }
}
