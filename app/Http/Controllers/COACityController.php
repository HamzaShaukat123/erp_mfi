<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ac_city;


class COACityController extends Controller
{
     public function index()
    {
         $accCity = ac_city::where('status', 1)
        ->get();
        return view('ac.acc_city',compact('accCity'));
    }


    public function store(Request $request)
    {
        $acc_city = new ac_city();
        $acc_city->created_by = session('user_id');

        if ($request->has('acc_city_name') && $request->acc_city_name) {
            $acc_city->city=$request->acc_city_name;
        }

        $acc_city->save();
        return redirect()->route('all-city');
    }


    public function destroy(Request $request)
    {
        $ac_city = ac_city::where('id', $request->city_cod)->update([
            'status' => '0',
            'updated_by' => session('user_id'),
        ]);
        return redirect()->route('all-city');
    }


    public function update(Request $request)
    {
        $ac_city = ac_city::find($request->city_cod);
        if ($ac_city) {
            $ac_city->city = $request->city_name;
            $ac_city->updated_by = session('user_id');
            $ac_city->save();
        }
        return redirect()->route('all-city');
    }


    public function show(Request $request)
    {
        $city = ac_city::find($request->id);
        return response()->json($city);
    }


}
