<?php

namespace App\Http\Controllers;


use App\Models\AC;
use App\Services\myPDF;
use App\Models\lager;
use App\Models\lager0;
use App\Models\jv2_att;
use App\Models\Sales;
use App\Models\Sales_2;
use App\Models\sales_ageing;
use App\Models\purchase_ageing;
use App\Models\vw_union_sale_1_2_opbal;
use App\Models\vw_union_pur_1_2_opbal;
use App\Models\pdc;
use App\Traits\SaveImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

class SalesAgeingController extends Controller
{

    public function index()
    {
        // Fetching sales ageing data with the relevant fields and the left join
        $jv2 = sales_ageing::select('sales_ageing.id', 'sales_ageing.voch_prefix','sales_ageing.status', 'sales_ageing.jv2_id', 'sales_ageing.sales_prefix', 'sales_ageing.sales_id', 'sales_ageing.amount', 'ac.ac_name')
            ->leftJoin('ac', 'sales_ageing.acc_name', '=', 'ac.ac_code')
        ->get();


        // Passing data to the view
        return view('salesageing.salesageing', compact('jv2'));

    }


    public function destroy(Request $request)
    {
        $lager0 = sales_ageing::where('id', $request->delete_id_no)->update([
            'status' => '0',
            'updated_by' => session('user_id'),
        ]);
        return redirect()->route('all-salesageing');
    }

}
