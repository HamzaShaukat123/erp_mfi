<?php

namespace App\Http\Controllers;

use TCPDF;

use Illuminate\Http\Request;
use App\Models\Item_entry2;
use App\Models\Item_Groups;
use App\Models\AC;
use App\Models\lager_much_op_bal;
use App\Models\tpurchase;
use App\Models\tpurchase_2;
use App\Models\pur2_att;
use App\Models\tax_tpurchase_2;
use Illuminate\Support\Facades\File;
use App\Traits\SaveImage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use App\Services\myPDF;
use Carbon\Carbon;

class Purchase2Controller extends Controller
{
    use SaveImage;

    public function index()
    {
        $pur2 = tpurchase::where('tpurchase.status',1)
        ->leftjoin ('tpurchase_2', 'tpurchase_2.sales_inv_cod' , '=', 'tpurchase.Sale_inv_no')
        ->join('ac as acc_name', 'acc_name.ac_code', '=', 'tpurchase.account_name')
        ->join('ac as disp_to', 'disp_to.ac_code', '=', 'tpurchase.Cash_pur_name_ac')
        ->leftjoin('tax_tpurchase_2', 'tax_tpurchase_2.sales_inv_cod', '=', 'tpurchase.Sale_inv_no')
        ->leftjoin('item_group', 'item_group.item_group_cod', '=', 'tax_tpurchase_2.item')
        ->select(
            'tpurchase.Sale_inv_no','tpurchase.sa_date','acc_name.ac_name as acc_name','tpurchase.pur_ord_no',
            'disp_to.ac_name as disp_to','tpurchase.Cash_pur_name','tpurchase.Sales_Remarks','tpurchase.sales_against','tpurchase.prefix',
            'tpurchase.ConvanceCharges','tpurchase.LaborCharges','tpurchase.Bill_discount','item_group.group_name', 'tpurchase.sales_against',
            \DB::raw('SUM(tpurchase_2.weight_pc * tpurchase_2.Sales_qty2) as weight_sum'),
            \DB::raw('SUM(((tpurchase_2.Sales_qty2 * tpurchase_2.sales_price) + ((tpurchase_2.Sales_qty2 * tpurchase_2.sales_price) * (tpurchase_2.discount/100))) * tpurchase_2.length) as total_bill')
        )
        ->groupby('tpurchase.Sale_inv_no','tpurchase.sa_date','acc_name.ac_name','tpurchase.pur_ord_no','item_group.group_name',
            'disp_to.ac_name','tpurchase.Cash_pur_name','tpurchase.Sales_Remarks','tpurchase.sales_against','tpurchase.prefix' ,
            'tpurchase.ConvanceCharges','tpurchase.LaborCharges','tpurchase.Bill_discount')
        ->get();
        // 'item_group.group_name'
        return view('purchase2.index',compact('pur2'));
    }



    public function indexPaginate()
    {
        $pur2 = tpurchase::where('tpurchase.status', 1)
        ->leftJoin('tpurchase_2', 'tpurchase_2.sales_inv_cod', '=', 'tpurchase.Sale_inv_no')
        ->join('ac as acc_name', 'acc_name.ac_code', '=', 'tpurchase.account_name')
        ->join('ac as disp_to', 'disp_to.ac_code', '=', 'tpurchase.Cash_pur_name_ac')
        ->leftJoin('tax_tpurchase_2', 'tax_tpurchase_2.sales_inv_cod', '=', 'tpurchase.Sale_inv_no')
        ->leftJoin('item_group', 'item_group.item_group_cod', '=', 'tax_tpurchase_2.item')
        ->select(
            'tpurchase.Sale_inv_no',
            'tpurchase.sa_date',
            'acc_name.ac_name as acc_name',
            'tpurchase.pur_ord_no',
            'disp_to.ac_name as disp_to',
            'tpurchase.Cash_pur_name',
            'tpurchase.Sales_Remarks',
            'tpurchase.sales_against',
            'tpurchase.prefix',
            'tpurchase.ConvanceCharges',
            'tpurchase.LaborCharges',
            'tpurchase.Bill_discount',
            'item_group.group_name',
            \DB::raw('SUM(tpurchase_2.weight_pc * tpurchase_2.Sales_qty2) as weight_sum'),
            \DB::raw('SUM(((tpurchase_2.Sales_qty2 * tpurchase_2.sales_price) + ((tpurchase_2.Sales_qty2 * tpurchase_2.sales_price) * (tpurchase_2.discount/100))) * tpurchase_2.length) as total_bill')
        )
        ->groupBy(
            'tpurchase.Sale_inv_no',
            'tpurchase.sa_date',
            'acc_name.ac_name',
            'tpurchase.pur_ord_no',
            'item_group.group_name',
            'disp_to.ac_name',
            'tpurchase.Cash_pur_name',
            'tpurchase.Sales_Remarks',
            'tpurchase.sales_against',
            'tpurchase.prefix',
            'tpurchase.ConvanceCharges',
            'tpurchase.LaborCharges',
            'tpurchase.Bill_discount'
        )
        ->orderBy('tpurchase.Sale_inv_no', 'desc') // Order by date, latest first
        ->paginate(100); // Paginate with 100 records per page


        // 'item_group.group_name'
        return view('purchase2.index',compact('pur2'));
    }

   public function create(Request $request)
    {
        $items = Item_entry2::all();
        $item_group = Item_Groups::all();
        $item_group = Item_Groups::whereBetween('item_group_cod', [1, 6])->get();
        $coa = AC::all();
        return view('purchase2.create',compact('items','coa','item_group'));
    }

    
    public function store(Request $request)
    {
        
        $pur2 = new tpurchase();
        $pur2->created_by = session('user_id');

        if ($request->has('sa_date') && $request->sa_date) {
            $pur2->sa_date=$request->sa_date;
        }
        if ($request->has('pur_ord_no') && $request->pur_ord_no) {
            $pur2->pur_ord_no=$request->pur_ord_no;
        }
        if ($request->has('isInduced') && $request->isInduced==1) {
            $pur2->sale_against=$request->sale_against;
        }
        if ($request->has('sales_against') && $request->sales_against) {
            $pur2->sales_against=$request->sales_against;
        }
        if ($request->has('account_name') && $request->account_name) {
            $pur2->account_name=$request->account_name;
        }
        if ($request->has('disp_account_name') && $request->disp_account_name) {
            $pur2->Cash_pur_name_ac=$request->disp_account_name;
        }
        if ($request->has('Cash_pur_name') && $request->Cash_pur_name) {
            $pur2->Cash_pur_name=$request->Cash_pur_name;
        }
        if ($request->has('cash_Pur_address') && $request->cash_Pur_address) {
            $pur2->cash_Pur_address=$request->cash_Pur_address;
        }
        if ($request->has('Sales_Remarks') && $request->Sales_Remarks) {
            $pur2->Sales_Remarks=$request->Sales_Remarks;
        }
        if ($request->has('ConvanceCharges') && $request->ConvanceCharges) {
            $pur2->ConvanceCharges=$request->ConvanceCharges;
        }
        if ($request->has('LaborCharges') && $request->LaborCharges) {
            $pur2->LaborCharges=$request->LaborCharges;
        }
        if ($request->has('Bill_discount') && $request->Bill_discount) {
            $pur2->Bill_discount=$request->Bill_discount;
        }

        $pur2->save();

        $pur_2_id = tpurchase::latest()->first();

        if($request->has('items'))
        {
            for($i=0;$i<$request->items;$i++)
            {
                if(filled($request->item_name[$i]))
                {
                    $tpurchase_2 = new tpurchase_2();

                    $tpurchase_2->sales_inv_cod=$pur_2_id['Sale_inv_no'];
                    $tpurchase_2->item_cod=$request->item_cod[$i];

                    if ($request->remarks[$i]!=null) {
                        $tpurchase_2->remarks=$request->remarks[$i];
                    }
                    if ($request->pur2_qty2[$i]!=null) {
                        $tpurchase_2->Sales_qty2=$request->pur2_qty2[$i];
                    }
                    if ($request->pur2_per_unit[$i]!=null) {
                        $tpurchase_2->sales_price=$request->pur2_per_unit[$i];
                    }
                    if ($request->weight_per_piece[$i]!=null) {
                        $tpurchase_2->weight_pc=$request->weight_per_piece[$i];
                    }
                    if ($request->pur2_len[$i]!=null) {
                        $tpurchase_2->length=$request->pur2_len[$i];
                    }
                    if ($request->pur2_price_date[$i]!=null) {
                        $tpurchase_2->rat_dat=$request->pur2_price_date[$i];
                    }
                    if ($request->pur2_percentage[$i]!=null) {
                        $tpurchase_2->discount=$request->pur2_percentage[$i];
                    }
                    
                    $tpurchase_2->save();
                }
            }
        }

        $toggleValue = $request->isCommissionForm;
         
        if ($toggleValue === "1") {

            $tax_pur2 = new tax_tpurchase_2();

            $tax_pur2->sales_inv_cod=$pur_2_id['Sale_inv_no'];

            if ($request->has('bamount') && $request->bamount) {
                $tax_pur2->bamount=$request->bamount;
            }
            if ($request->has('disc') && $request->disc) {
                $tax_pur2->disc=$request->disc;
            }
            if ($request->has('cd_disc') && $request->cd_disc) {
                $tax_pur2->cd_disc=$request->cd_disc;
            }
            if ($request->has('comm_disc') && $request->comm_disc) {
                $tax_pur2->comm_disc=$request->comm_disc;
            }
            if ($request->has('gst') && $request->gst) {
                $tax_pur2->gst=$request->gst;
            }
            if ($request->has('income_tax') && $request->income_tax) {
                $tax_pur2->income_tax=$request->income_tax;
            }
            if ($request->has('comm_amount') && $request->comm_amount) {
                $tax_pur2->comm_amount=$request->comm_amount;
            }
            if ($request->has('tax_item_name') && $request->tax_item_name) {
                $tax_pur2->item=$request->tax_item_name;
            }
            if ($request->has('tax_remarks') && $request->tax_remarks) {
                $tax_pur2->remarks=$request->tax_remarks;
            }

            $tax_pur2->save();
        }

        if($request->hasFile('att')){
            $files = $request->file('att');
            foreach ($files as $file)
            {
                $pur2Att = new pur2_att();
                $pur2Att->pur2_id = $pur_2_id['Sale_inv_no'];
                $extension = $file->getClientOriginalExtension();
                $pur2Att->att_path = $this->pur2Doc($file,$extension);
                $pur2Att->save();
            }
        }

        return redirect()->route('all-purchases2-paginate');
    }

    public function edit($id)
    {
        $item_group = Item_Groups::all();
        $items = Item_entry2::orderBy('item_name', 'asc')->get();
        $coa = AC::orderBy('ac_name', 'asc')->get();

        $pur2 = tpurchase::where('tpurchase.Sale_inv_no',$id)
        ->leftjoin('tax_tpurchase_2', 'tax_tpurchase_2.sales_inv_cod', '=', 'tpurchase.Sale_inv_no')
        ->select(
            'tpurchase.Sale_inv_no','tpurchase.sa_date','tpurchase.pur_ord_no', 'tpurchase.Cash_pur_name','tpurchase.Sales_Remarks','tpurchase.sales_against',
            'tpurchase.ConvanceCharges','tpurchase.cash_Pur_address','tpurchase.LaborCharges','tpurchase.Bill_discount','tpurchase.prefix','tpurchase.account_name',
            'tpurchase.Cash_pur_name_ac','bamount', 'disc', 'item', 'comm_amount', 'comm_disc', 'cd_disc','tax_id','income_tax','gst',
            'tax_tpurchase_2.remarks as tax_remarks'
        )
        ->groupby('tpurchase.Sale_inv_no','tpurchase.sa_date','tpurchase.pur_ord_no','tpurchase.Cash_pur_name',
        'tpurchase.Sales_Remarks','tpurchase.cash_Pur_address','tpurchase.sales_against','tpurchase.ConvanceCharges','tpurchase.account_name',
        'tpurchase.LaborCharges','tpurchase.Bill_discount','tpurchase.prefix','tpurchase.Cash_pur_name_ac','bamount', 'disc',
        'item', 'comm_amount', 'comm_disc', 'cd_disc','tax_id', 'income_tax','gst','tax_remarks' )
        ->first();

        $pur2_item = tpurchase_2::where('tpurchase_2.sales_inv_cod',$id)->get();

        return view('purchase2.edit',compact('pur2','pur2_item','items','coa','item_group'));
    }

    public function update(Request $request){

        $pur2 = tpurchase::where('Sale_inv_no',$request->pur2_id)->get()->first();

        if ($request->has('sa_date') && $request->sa_date) {
            $pur2->sa_date=$request->sa_date;
        }
        if ($request->has('pur_ord_no') && $request->pur_ord_no OR empty($request->pur_ord_no)) {
            $pur2->pur_ord_no=$request->pur_ord_no;
        }
        if ($request->has('hidden_sales_against') && $request->hidden_sales_against OR empty($request->hidden_sales_against)) {
            $pur2->sales_against=$request->hidden_sales_against;
        }
        if ($request->has('account_name') && $request->account_name) {
            $pur2->account_name=$request->account_name;
        }
        if ($request->has('disp_account_name') && $request->disp_account_name) {
            $pur2->Cash_pur_name_ac=$request->disp_account_name;
        }
        if ($request->has('Cash_pur_name') && $request->Cash_pur_name OR empty($request->Cash_pur_name)) {
            $pur2->Cash_pur_name=$request->Cash_pur_name;
        }
        if ($request->has('cash_Pur_address') && $request->cash_Pur_address OR empty($request->cash_Pur_address)) {
            $pur2->cash_Pur_address=$request->cash_Pur_address;
        }
        if ($request->has('Sales_Remarks') && $request->Sales_Remarks OR empty($request->Sales_Remarks)) {
            $pur2->Sales_Remarks=$request->Sales_Remarks;
        }
        if ($request->has('ConvanceCharges') && $request->ConvanceCharges OR $request->ConvanceCharges==0) {
            $pur2->ConvanceCharges=$request->ConvanceCharges;
        }
        if ($request->has('LaborCharges') && $request->LaborCharges OR $request->LaborCharges==0) {
            $pur2->LaborCharges=$request->LaborCharges;
        }
        if ($request->has('Bill_discount') && $request->Bill_discount OR $request->Bill_discount==0) {
            $pur2->Bill_discount=$request->Bill_discount;
        }

        tpurchase::where('Sale_inv_no', $request->pur2_id)->update([
            'sa_date'=>$pur2->sa_date,
            'pur_ord_no'=>$pur2->pur_ord_no,
            'sales_against'=>$pur2->sales_against,
            'account_name'=>$pur2->account_name,
            'Cash_pur_name_ac'=>$pur2->Cash_pur_name_ac,
            'Cash_pur_name'=>$pur2->Cash_pur_name,
            'cash_Pur_address'=>$pur2->cash_Pur_address,
            'Sales_Remarks'=>$pur2->Sales_Remarks,
            'ConvanceCharges'=>$pur2->ConvanceCharges,
            'LaborCharges'=>$pur2->LaborCharges,
            'Bill_discount'=>$pur2->Bill_discount,
            'updated_by' => session('user_id'),
        ]);

        tpurchase_2::where('sales_inv_cod', $request->pur2_id)->delete();

        if($request->has('items'))
        {
            for($i=0;$i<$request->items;$i++)
            {
                if(filled($request->item_name[$i]))
                {
                    $tpurchase_2 = new tpurchase_2();

                    $tpurchase_2->sales_inv_cod=$request->pur2_id;
                    $tpurchase_2->item_cod=$request->item_cod[$i];

                    if ($request->remarks[$i]!=null OR empty($request->remarks[$i])) {
                        $tpurchase_2->remarks=$request->remarks[$i];
                    }
                    if ($request->pur2_qty2[$i]!=null) {
                        $tpurchase_2->Sales_qty2=$request->pur2_qty2[$i];
                    }
                    if ($request->pur2_per_unit[$i]!=null) {
                        $tpurchase_2->sales_price=$request->pur2_per_unit[$i];
                    }
                    if ($request->weight_per_piece[$i]!=null) {
                        $tpurchase_2->weight_pc=$request->weight_per_piece[$i];
                    }
                    if ($request->pur2_len[$i]!=null) {
                        $tpurchase_2->length=$request->pur2_len[$i];
                    }
                    if ($request->pur2_price_date[$i]!=null OR empty($request->pur2_price_date[$i])) {
                        $tpurchase_2->rat_dat=$request->pur2_price_date[$i];
                    }
                    if ($request->pur2_percentage[$i]!=null) {
                        $tpurchase_2->discount=$request->pur2_percentage[$i];
                    }
                    
                    $tpurchase_2->save();
                }
            }
        }

        $toggleValue = $request->isCommissionForm;

        if ($toggleValue === "1" ) {
            
            $tax_pur2 = tax_tpurchase_2::where('sales_inv_cod',$request->pur2_id)->get()->first();

            if(is_null($tax_pur2)){

                $new_tax_pur2 = new tax_tpurchase_2();

                $new_tax_pur2->sales_inv_cod=$request->pur2_id;
    
                if ($request->has('bamount') && $request->bamount OR $request->bamount==0) {
                    $new_tax_pur2->bamount=$request->bamount;
                }
                if ($request->has('disc') && $request->disc OR $request->disc==0) {
                    $new_tax_pur2->disc=$request->disc;
                }
                if ($request->has('cd_disc') && $request->cd_disc OR $request->cd_disc==0) {
                    $new_tax_pur2->cd_disc=$request->cd_disc;
                }
                if ($request->has('comm_disc') && $request->comm_disc OR $request->comm_disc==0) {
                    $new_tax_pur2->comm_disc=$request->comm_disc;
                }
                if ($request->has('gst') && $request->gst OR $request->gst==0) {
                    $new_tax_pur2->gst=$request->gst;
                }
                if ($request->has('income_tax') && $request->income_tax OR $request->income_tax==0) {
                    $new_tax_pur2->income_tax=$request->income_tax;
                }
                if ($request->has('comm_amount') && $request->comm_amount OR $request->comm_amount==0) {
                    $new_tax_pur2->comm_amount=$request->comm_amount;
                }
                if ($request->has('tax_item_name') && $request->tax_item_name) {
                    $new_tax_pur2->item=$request->tax_item_name;
                }
                if ($request->has('tax_remarks') && $request->tax_remarks OR empty($request->tax_remarks)) {
                    $new_tax_pur2->remarks=$request->tax_remarks;
                }
    
                $new_tax_pur2->save();
            }

            else{
                if ($request->has('bamount') && $request->bamount OR $request->bamount==0) {
                    $tax_pur2->bamount=$request->bamount;
                }
                if ($request->has('disc') && $request->disc OR $request->disc==0) {
                    $tax_pur2->disc=$request->disc;
                }
                if ($request->has('cd_disc') && $request->cd_disc OR $request->cd_disc==0) {
                    $tax_pur2->cd_disc=$request->cd_disc;
                }
                if ($request->has('comm_disc') && $request->comm_disc OR $request->comm_disc==0) {
                    $tax_pur2->comm_disc=$request->comm_disc;
                }
                if ($request->has('gst') && $request->gst OR $request->gst==0) {
                    $tax_pur2->gst=$request->gst;
                }
                if ($request->has('income_tax') && $request->income_tax OR $request->income_tax==0) {
                    $tax_pur2->income_tax=$request->income_tax;
                }
                if ($request->has('comm_amount') && $request->comm_amount OR $request->comm_amount==0) {
                    $tax_pur2->comm_amount=$request->comm_amount;
                }
                if ($request->has('tax_item_name') && $request->tax_item_name) {
                    $tax_pur2->item=$request->tax_item_name;
                }
                if ($request->has('tax_remarks') && $request->tax_remarks  OR empty($request->tax_remarks)) {
                    $tax_pur2->remarks=$request->tax_remarks;
                }

                tax_tpurchase_2::where('sales_inv_cod', $request->pur2_id)->update([
                    'bamount'=>$tax_pur2->bamount,
                    'disc'=>$tax_pur2->disc,
                    'cd_disc'=>$tax_pur2->cd_disc,
                    'comm_disc'=>$tax_pur2->comm_disc,
                    'gst'=>$tax_pur2->gst,
                    'income_tax'=>$tax_pur2->income_tax,
                    'comm_amount'=>$tax_pur2->comm_amount,
                    'item'=>$tax_pur2->item,
                    'remarks'=>$tax_pur2->remarks,
                ]);
            }
        }


        if($request->hasFile('att')){
            $files = $request->file('att');
            foreach ($files as $file)
            {
                $pur2Att = new pur2_att();
                $pur2Att->pur2_id = $request->pur2_id;
                $extension = $file->getClientOriginalExtension();
                $pur2Att->att_path = $this->pur2Doc($file,$extension);
                $pur2Att->save();
            }
        }

        return redirect()->route('all-purchases2-paginate');
    }

    public function addAtt(Request $request)
    {
        $pur2_id=$request->att_id;

        if($request->hasFile('addAtt')){
            $files = $request->file('addAtt');
            foreach ($files as $file)
            {
                $pur2Att = new pur2_att();
                $pur2Att->created_by = session('user_id');
                $pur2Att->pur2_id = $pur2_id;
                $extension = $file->getClientOriginalExtension();
                $pur2Att->att_path = $this->pur2Doc($file,$extension);
                $pur2Att->save();
            }
        }
        return redirect()->route('all-purchases2-paginate');

    }
    
    public function destroy(Request $request)
    {
        tpurchase::where('Sale_inv_no', $request->delete_purc2)->update([
            'status' => '0',
            'updated_by' => session('user_id'),
        ]);
        return redirect()->route('all-purchases2-paginate');
    }

    public function show(string $id)
    {
        $pur = tpurchase::where('Sale_inv_no',$id)
                ->join('ac as acc_name','tpurchase.account_name','=','acc_name.ac_code')
                ->join('ac as dispt_to','tpurchase.Cash_pur_name_ac','=','dispt_to.ac_code')
                ->select('tpurchase.*','dispt_to.ac_name as disp_to','acc_name.ac_name as ac_name', 
                'acc_name.address as address', 'acc_name.phone_no as phone_no')
                ->first();

        $pur2 = tpurchase_2::where('sales_inv_cod',$id)
                ->join('item_entry2 as ie','tpurchase_2.item_cod','=','ie.it_cod')
                ->select('tpurchase_2.*','ie.item_name')
                ->get();

        return view('purchase2.view',compact('pur','pur2'));
    }

    public function getAttachements(Request $request)
    {
        $pur2_atts = pur2_att::where('pur2_id', $request->id)->get();
        
        return $pur2_atts;
    }

    public function getunclosed()
    {
        $unclosed_inv = tpurchase::where(function ($query) {
            $query->where('sales_against', '')
                  ->orWhereNull('sales_against')
                  ->where('tpurchase.status',1)
                  ->where('tpurchase.Cash_pur_name_ac', '!=', 24);

        })
        ->join('ac', 'ac.ac_code', '=', 'tpurchase.account_name')
        ->join('ac as dispt_acc', 'dispt_acc.ac_code', '=', 'tpurchase.Cash_pur_name_ac')
        ->select('tpurchase.*', 'ac.ac_name as acc_name','dispt_acc.ac_name as disp_acc')  // Select fields from both tables as needed
        ->orderBy('tpurchase.sa_date') 
        ->get();
        return $unclosed_inv;
    }

    public function getunclosedstockin()
    {
        $unclosed_inv = tpurchase::where(function ($query) {
            $query->where('sales_against', '')
                  ->orWhereNull('sales_against')
                  ->where('tpurchase.status',1)
                  ->where('tpurchase.Cash_pur_name_ac',24);

        })
        ->join('ac', 'ac.ac_code', '=', 'tpurchase.account_name')
        ->join('ac as dispt_acc', 'dispt_acc.ac_code', '=', 'tpurchase.Cash_pur_name_ac')
        ->select('tpurchase.*', 'ac.ac_name as acc_name','dispt_acc.ac_name as disp_acc')  // Select fields from both tables as needed
        ->orderBy('tpurchase.sa_date')
        ->get();
        return $unclosed_inv;
    }

    public function getItems($id){

        $pur1= tpurchase::where('Sale_inv_no',$id)->get()->first();

        $pur2 = tpurchase_2::where('sales_inv_cod',$id)
        ->leftjoin('item_entry as ie','tpurchase_2.item_cod','=','ie.it_cod')
        ->select('tpurchase_2.*','ie.item_name')
        ->get();

        return response()->json([
            'pur1' => $pur1,
            'pur2' => $pur2,
        ]);
    }

    public function deleteAtt($id)
    {
        $doc=pur2_att::where('att_id', $id)->select('att_path')->first();
        $filePath = public_path($doc['att_path']);
        if (File::exists($filePath)) {
            File::delete($filePath);
            $pur2_att = pur2_att::where('att_id', $id)->delete();
            return response()->json(['message' => 'File deleted successfully.']);
        } else {
            return response()->json(['message' => 'File not found.'], 404);
        }	
    }

    public function view($id)
    {
        $doc=pur2_att::where('att_id', $id)->select('att_path')->first();
        $filePath = public_path($doc['att_path']);
        if (file_exists($filePath)) {
            return Response::file($filePath);
        } 
    }

    public function downloadAtt($id)
    {
        $doc=pur2_att::where('att_id', $id)->select('att_path')->first();
        $filePath = public_path($doc['att_path']);
        if (file_exists($filePath)) {
            return Response::download($filePath);
        } 
    }

    public function generatePDF($id)
    {
        $purchase = tpurchase::where('Sale_inv_no',$id)
        ->join('ac','tpurchase.account_name','=','ac.ac_code')
        ->first();

        $purchase_items = tpurchase_2::where('sales_inv_cod',$id)
                ->join('item_entry2','tpurchase_2.item_cod','=','item_entry2.it_cod')
                ->select('tpurchase_2.*','item_entry2.item_name')
                ->get();

        $pdf = new MyPDF();

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('MFI');
        $pdf->SetTitle('Purchase Invoice-'.$purchase['prefix'].$purchase['Sale_inv_no']);
        $pdf->SetSubject('Purchase Invoice-'.$purchase['prefix'].$purchase['Sale_inv_no']);
        $pdf->SetKeywords('Purchase Invoice, TCPDF, PDF');
                   
        // Add a page
        $pdf->AddPage();
           
        $pdf->setCellPadding(1.2); // Set padding for all cells in the table

        // margin top
        $margin_top = '.margin-top {
            margin-top: 10px;
        }';
        // $pdf->writeHTML('<style>' . $margin_top . '</style>', true, false, true, false, '');

        // margin bottom
        $margin_bottom = '.margin-bottom {
            margin-bottom: 4px;
        }';

        // $pdf->writeHTML('<style>' . $margin_bottom . '</style>', true, false, true, false, '');

        $heading='<h1 style="font-size:20px;text-align:center;font-style:italic;text-decoration:underline;color:#17365D">Purchase Invoice</h1>';
        $pdf->writeHTML($heading, true, false, true, false, '');
        $pdf->writeHTML('<style>' . $margin_bottom . '</style>', true, false, true, false, '');

        $html = '<table style="margin-bottom:1rem">';
        $html .= '<tr>';
        $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Invoice No: &nbsp;<span style="text-decoration: underline;color:#000">'.$purchase['prefix'].$purchase['Sale_inv_no'].'</span></td>';
        $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Date: &nbsp;<span style="color:#000">'.\Carbon\Carbon::parse($purchase['sa_date'])->format('d-m-y').'</span></td>';
        $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Mill Inv No: <span style="text-decoration: underline;color:#000">'.$purchase['pur_ord_no'].'</span></td>';
        // html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Login: &nbsp; <span style="text-decoration: underline;color:#000">Hamza</span></td>';
        $html .= '</tr>';
        $html .= '</table>';

        // $pdf->writeHTML($html, true, false, true, false, '');

        $html .= '<table border="0.1px" style="border-collapse: collapse;">';
        $html .= '<tr>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Account Name </td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['ac_name'].'</td>';
        $html .= '<td width="20%" width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Name Of Person</td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['Cash_pur_name'].'</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td width="20%" width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D" >Address </td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['address'].'</td>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Persons Address</td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['cash_Pur_address'].'</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Phone </td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['phone_no'].'</td>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Persons Phone</td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['cash_pur_phone'].'</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Remarks </td>';
        $html .= '<td width="80%" style="font-size:10px;font-family:poppins;">'.$purchase['Sales_Remarks'].'</td>';
        $html .= '</tr>';
        $html .= '</table>';
        
        $pdf->writeHTML($html, true, false, true, false, '');
    
        $html = '<table border="0.3" style="text-align:center;margin-top:10px">';
        $html .= '<tr>';
        $html .= '<th style="width:6%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">S/R</th>';
        $html .= '<th style="width:26%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Item Name</th>';
        $html .= '<th style="width:20%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Description</th>';
        $html .= '<th style="width:10%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Qty</th>';
        $html .= '<th style="width:11%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Price/Unit</th>';
        $html .= '<th style="width:7%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Len</th>';
        $html .= '<th style="width:7%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">%</th>';
        $html .= '<th style="width:13%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Amount</th>';
        $html .= '</tr>';
        $html .= '</table>';

        $pdf->setTableHtml($html);

        $count = 1;
        $total_weight = 0;
        $total_quantity = 0;
        $total_amount = 0;

        $html .= '<table cellspacing="0" cellpadding="5">';
        foreach ($purchase_items as $items) {
            // Determine background color based on odd/even rows
            $bg_color = ($count % 2 == 0) ? 'background-color:#f1f1f1' : '';

            $html .= '<tr style="' . $bg_color . '">';
            $html .= '<td style="width:6%;border-right:1px dashed #000;border-left:1px dashed #000; text-align:center">' . $count . '</td>';
            $html .= '<td style="width:26%;border-right:1px dashed #000">' . $items['item_name'] . '</td>';
            $html .= '<td style="width:20%;border-right:1px dashed #000; font-size:9px;">' . $items['remarks'] . '</td>';
            $html .= '<td style="width:10%;border-right:1px dashed #000; text-align:center">' . $items['Sales_qty2'] . '</td>';
            $total_quantity += $items['Sales_qty2'];
            $html .= '<td style="width:11%;border-right:1px dashed #000; text-align:center">' . $items['sales_price'] . '</td>';
            $html .= '<td style="width:7%;border-right:1px dashed #000; text-align:center">' . $items['length'] . '</td>';
            $html .= '<td style="width:7%;border-right:1px dashed #000; text-align:center">' . $items['discount'] . '</td>';

            // Calculate the total weight and amount
            $total_weight += $items['Sales_qty2'] * $items['weight_pc'];
            $amount = (($items['Sales_qty2'] * $items['sales_price']) + (($items['Sales_qty2'] * $items['sales_price']) * ($items['discount'] / 100))) * $items['length'];
            $html .= '<td style="width:13%;border-right:1px dashed #000; text-align:center">' . $amount . '</td>';
            $total_amount += $amount;

            $html .= '</tr>';
            $count++;
        }
        $html .= '</table>';

        $pdf->writeHTML($html, true, false, true, false, '');
        $currentY = $pdf->GetY();
            
        if(($pdf->getPageHeight()-$pdf->GetY())<57){
            $pdf->AddPage();
            $currentY = $pdf->GetY()+15;
        }

        $pdf->SetFont('helvetica','B', 10);
        $pdf->SetTextColor(23, 54, 93);

        $pdf->SetXY(10, $currentY);
        $pdf->Cell(40, 5, 'Total Weight(kg)', 1,1);
        $pdf->Cell(40, 5, 'Total Quantity', 1,1);

        // // Column 2
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetXY(50, $currentY);
        $pdf->Cell(42, 5,  $total_weight, 1, 'R');
        $pdf->SetXY(50, $currentY+6.8);
        $pdf->SetFont('helvetica','', 10);

        $pdf->Cell(42, 5, $total_quantity, 1,'R');

        $roundedTotal= round($total_amount+$purchase['LaborCharges']+$purchase['ConvanceCharges']-$purchase['Bill_discount']);
        $num_to_words=$pdf->convertCurrencyToWords($roundedTotal);
       

        // Column 3
        $pdf->SetFont('helvetica','B', 10);
        $pdf->SetTextColor(23, 54, 93);

        $pdf->SetXY(120, $currentY);
        $pdf->Cell(45, 5, 'Total Amount', 1,1);
        $pdf->SetXY(120, $currentY+6.8);
        $pdf->Cell(45, 5, 'Labour Charges', 1,1);
        $pdf->SetXY(120, $currentY+13.7);
        $pdf->Cell(45, 5, 'Convance Charges', 1,1);
        $pdf->SetXY(120, $currentY+20.5);
        $pdf->Cell(45, 5, 'Discount(Rs)', 1,1);
        // $pdf->SetXY(120, $currentY+27.3);
        // $pdf->Cell(45, 5, 'Net Amount', 1,1);
        // Change font size to 12 for "Net Amount"
        $pdf->SetFont('helvetica', 'B', 12);  
        $pdf->SetXY(120, $currentY+27.3);
        $pdf->Cell(45, 5, 'Net Amount', 1, 1);
        
        // // Column 4
        $pdf->SetFont('helvetica','', 10);
        $pdf->SetTextColor(0, 0, 0);

        $pdf->SetXY(165, $currentY);
        $pdf->Cell(35, 5, $total_amount, 1, 'R');
        $pdf->SetXY(165, $currentY+6.8);
        $pdf->Cell(35, 5, $purchase['LaborCharges'], 1, 'R');
        $pdf->SetXY(165, $currentY+13.7);
        $pdf->Cell(35, 5, $purchase['ConvanceCharges'], 1, 'R');
        $pdf->SetXY(165, $currentY+20.5);
        $pdf->Cell(35, 5, $purchase['Bill_discount'], 1, 'R');
        $pdf->SetXY(165, $currentY+27.3);
        $net_amount=number_format(round($total_amount+$purchase['LaborCharges']+$purchase['ConvanceCharges']-$purchase['Bill_discount']));
        $pdf->SetFont('helvetica','B', 12);
        $pdf->Cell(35, 5,  $net_amount, 1, 'R');
        
        $pdf->SetFont('helvetica','BIU', 14);
        $pdf->SetTextColor(23, 54, 93);

        $pdf->SetXY(10, $currentY+20);
        $width = 100;
        $pdf->MultiCell($width, 10, $num_to_words, 0, 'L', 0, 1, '', '', true);
        $pdf->SetFont('helvetica','', 10);
        
        // Close and output PDF
        $pdf->Output('Purchase Invoice_'.$purchase['prefix'].$purchase['Sale_inv_no'].'.pdf', 'I');
    }
}
