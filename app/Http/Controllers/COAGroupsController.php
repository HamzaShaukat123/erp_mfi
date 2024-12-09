<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ac_group;

class COAGroupsController extends Controller
{
    //
    public function index()
    {
        $accGroups = ac_group::where('status', 1)
        ->get();
        return view('ac.acc_groups',compact('accGroups'));
    }

    public function store(Request $request)
    {
        $acc_group = new ac_group();
        $acc_group->created_by = session('user_id');

        if ($request->has('acc_group_name') && $request->acc_group_name) {
            $acc_group->group_name=$request->acc_group_name;
        }

        $acc_group->save();
        return redirect()->route('all-acc-groups');
    }
    
    public function destroy(Request $request)
    {
        $ac_group = ac_group::where('group_cod', $request->acc_group_cod)->update([
            'status' => '0',
            'updated_by' => session('user_id'),
        ]);
        return redirect()->route('all-acc-groups');
    }

    public function update(Request $request)
    {
        $ac_group = ac_group::where('group_cod', $request->group_cod)->get();

        if ($request->has('group_name') && $request->group_name) {
            $ac_group->group_name=$request->group_name;
        }
               
        ac_group::where('group_cod', $request->group_cod)->update([
            'group_name'=>$ac_group->group_name,
            'updated_by' => session('user_id'),
        ]);

        return redirect()->route('all-acc-groups');
    }

    public function getDetails(Request $request)
    {
        $acc__group_details = ac_group::where('group_cod', $request->id)->get()->first();
        return $acc__group_details;
    }

}
