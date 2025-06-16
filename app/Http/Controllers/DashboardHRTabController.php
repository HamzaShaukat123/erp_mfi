<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\dash_pur_2_summary_monthly_companywise;
use App\Models\sale_pipe_summary_of_party_by_mill;
use App\Models\pur_pipe_summary_of_party_by_mill;
use App\Models\AC;
use App\Models\top_customers_of_sale2;
use App\Models\top_customers_of_pur2;
use App\Models\total_pur_pipe_summary_of_by_mill;

class DashboardHRTabController extends Controller
{
    public function HR(Request $request){
        $dash_pur_2_summary_monthly_companywise_for_donut = dash_pur_2_summary_monthly_companywise::where('dat2',$request->month)
        ->get();

        $dash_pur_2_summary_monthly_companywise = dash_pur_2_summary_monthly_companywise::get();

        $steelex = pur_pipe_summary_of_party_by_mill::leftjoin('ac','ac.ac_code','=','pur_pipe_summary_of_party_by_mill.account_name')
        ->where('dat',$request->month)
        ->whereIn('company_code', [86, 296])
        ->orderBy('weight', 'desc')
        ->get();

        $spm = pur_pipe_summary_of_party_by_mill::leftjoin('ac','ac.ac_code','=','pur_pipe_summary_of_party_by_mill.account_name')
        ->where('dat',$request->month)
        ->where('company_code',82)
        ->orderBy('weight', 'desc')
        ->get();

        $mehboob = pur_pipe_summary_of_party_by_mill::leftjoin('ac','ac.ac_code','=','pur_pipe_summary_of_party_by_mill.account_name')
        ->where('dat',$request->month)
        ->where('company_code',73)
        ->orderBy('weight', 'desc')
        ->get();

        $godown = sale_pipe_summary_of_party_by_mill::leftjoin('ac','ac.ac_code','=','sale_pipe_summary_of_party_by_mill.account_name')
        ->where('dat',$request->month)
        ->where('company_code',24)
        ->orderBy('weight', 'desc')
        ->get();

        $top_customers_of_pur2 = top_customers_of_pur2::leftjoin('ac','ac.ac_code','=','top_customers_of_pur2.account_name')
        ->where('dat',$request->month)
        ->get();


        $pur2summary = total_pur_pipe_summary_of_by_mill::where('dat',$request->month)
        ->orderBy('weight', 'desc')
        ->get();

        
        return [
            'dash_pur_2_summary_monthly_companywise' => $dash_pur_2_summary_monthly_companywise,
            'dash_pur_2_summary_monthly_companywise_for_donut' => $dash_pur_2_summary_monthly_companywise_for_donut,
            'steelex' => $steelex,
            'spm' => $spm,
            'mehboob' => $mehboob,
            'godown' => $godown,
            'top_customers_of_pur2' => $top_customers_of_pur2,
            'pur2summary' => $pur2summary
        ];
    }

    public function monthlyTonage(Request $request){
        $dash_pur_2_summary_monthly_companywise = dash_pur_2_summary_monthly_companywise::where('dat2',$request->month)
        ->get();

        return $dash_pur_2_summary_monthly_companywise;
    }

    public function monthlyTonageOfCustomer(Request $request){
        $pur_pipe_summary_of_party_by_mill = pur_pipe_summary_of_party_by_mill::where('dat',$request->month)
        ->where('account_name',$request->acc_name)
        ->get();

        return $pur_pipe_summary_of_party_by_mill;
    }
}
