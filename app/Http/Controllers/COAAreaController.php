<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ac_area;


class COAAreaController extends Controller
{
    public function index()
   {
        $accArea = ac_area::where('status', 1)
       ->get();
       return view('ac.acc_area',compact('accArea'));
   }


   public function store(Request $request)
   {
       $acc_area = new ac_area();
       $acc_area->created_by = session('user_id');

       if ($request->has('acc_area_name') && $request->acc_area_name) {
           $acc_area->area=$request->acc_area_name;
       }

       $acc_area->save();
       return redirect()->route('all-area');
   }


   public function destroy(Request $request)
   {
       $ac_area = ac_area::where('id', $request->area_cod)->update([
           'status' => '0',
           'updated_by' => session('user_id'),
       ]);
       return redirect()->route('all-area');
   }


   public function update(Request $request)
   {
       $ac_area = ac_area::find($request->area_cod);
       if ($ac_area) {
           $ac_area->area = $request->area_name;
           $ac_area->updated_by = session('user_id');
           $ac_area->save();
       }
       return redirect()->route('all-area');
   }


   public function show(Request $request)
   {
       $area = ac_area::find($request->id);
       return response()->json($area);
   }


}
