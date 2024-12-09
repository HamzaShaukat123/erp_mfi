<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\sub_head_of_acc;
use App\Models\head_of_acc;

class COASubHeadsController extends Controller
{
    //
    public function index()
    {
        $subheads = sub_head_of_acc::where('status', 1)
        ->leftjoin('head_of_acc as hoa', 'hoa.id', '=', 'sub_head_of_acc.main')
        ->select('hoa.heads as name', 'sub_head_of_acc.id as id', 'sub_head_of_acc.sub as sub_name')
        ->get();

        $heads = head_of_acc::all();
        return view('ac.subheads',compact('subheads','heads'));
    }

    public function store(Request $request)
    {
        $subheads = new sub_head_of_acc();
        $subheads->created_by = session('user_id');

        if ($request->has('main') && $request->main) {
            $subheads->main=$request->main;
        }
        if ($request->has('sub') && $request->sub) {
            $subheads->sub=$request->sub;
        }
        $subheads->save();
        return redirect()->route('all-acc-sub-heads-groups');
    }
    
    public function destroy(Request $request)
    {
        $sub_head = sub_head_of_acc::where('id', $request->sub_head_id)->update([
            'status' => '0',
            'updated_by' => session('user_id'),
        ]);
        return redirect()->route('all-acc-sub-heads-groups');
    }

    public function update(Request $request)
    {
       
        $sub_head = sub_head_of_acc::where('id', $request->sub_head_id)->get();

        if ($request->has('main') && $request->main) {
            $sub_head->main=$request->main;
        }
        if ($request->has('sub') && $request->sub) {
            $sub_head->sub=$request->sub;
        }
               
        sub_head_of_acc::where('id', $request->sub_head_id)->update([
            'main'=>$sub_head->main,
            'sub'=>$sub_head->sub,
            'updated_by' => session('user_id'),
        ]);

        return redirect()->route('all-acc-sub-heads-groups');
    }

    public function getCOASubHeadDetails(Request $request)
    {
        $subheads = sub_head_of_acc::where('id', $request->id)->get()->first();
        return $subheads;
    }
}
