<?php

namespace App\Http\Controllers;

use TCPDF;

use Illuminate\Http\Request;
use App\Models\Item_entry2;
use App\Models\Item_Groups;
use App\Models\AC;
use App\Models\tsales;
use App\Models\tsales_2;
use App\Models\sale2_att;
use App\Models\tpurchase;
use App\Models\tstock_out;
use Illuminate\Support\Facades\File;
use App\Traits\SaveImage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use App\Services\myPDF;

class Sales2Controller extends Controller
{
    use SaveImage;

    public function index()
    {
        $pur2 = tsales::where('tsales.status',1)
        ->leftjoin ('tsales_2', 'tsales_2.sales_inv_cod' , '=', 'tsales.Sal_inv_no')
        ->join('ac as acc_name', 'acc_name.ac_code', '=', 'tsales.account_name')
        ->join('ac as comp_acc', 'comp_acc.ac_code', '=', 'tsales.company_name')
        ->select(
            'tsales.Sal_inv_no','tsales.sa_date','acc_name.ac_name as acc_name','tsales.pur_ord_no',
            'comp_acc.ac_name as comp_account','tsales.company_name','tsales.Sales_Remarks','tsales.Cash_name','tsales.pur_against','tsales.prefix',
            'tsales.ConvanceCharges','tsales.LaborCharges','tsales.Bill_discount', 'tsales.pur_against',
            \DB::raw('SUM(tsales_2.weight_pc * tsales_2.Sales_qty2) as weight_sum'),
            \DB::raw('SUM(((tsales_2.Sales_qty2 * tsales_2.sales_price) + ((tsales_2.Sales_qty2 * tsales_2.sales_price) * (tsales_2.discount/100))) * tsales_2.length) as total_bill')
        )
        ->groupby('tsales.Sal_inv_no','tsales.sa_date','acc_name','tsales.pur_ord_no',
            'comp_account','tsales.company_name','tsales.Sales_Remarks','tsales.pur_against','tsales.prefix',
            'tsales.ConvanceCharges','tsales.LaborCharges','tsales.Bill_discount', 'tsales.pur_against','tsales.Cash_name',)
        ->get();

        return view('sale2.index',compact('pur2'));
    }

    public function indexPaginate()
    {
        $pur2 = tsales::where('tsales.status', 1)
        ->leftJoin('tsales_2', 'tsales_2.sales_inv_cod', '=', 'tsales.Sal_inv_no')
        ->join('ac as acc_name', 'acc_name.ac_code', '=', 'tsales.account_name')
        ->join('ac as comp_acc', 'comp_acc.ac_code', '=', 'tsales.company_name')
        ->select(
            'tsales.Sal_inv_no',
            'tsales.sa_date',
            'acc_name.ac_name as acc_name',
            'tsales.pur_ord_no',
            'comp_acc.ac_name as comp_account',
            'tsales.company_name',
            'tsales.Sales_Remarks',
            'tsales.Cash_name',
            'tsales.pur_against',
            'tsales.prefix',
            'tsales.ConvanceCharges',
            'tsales.LaborCharges',
            'tsales.Bill_discount',
            \DB::raw('SUM(tsales_2.weight_pc * tsales_2.Sales_qty2) as weight_sum'),
            \DB::raw('SUM(((tsales_2.Sales_qty2 * tsales_2.sales_price) + ((tsales_2.Sales_qty2 * tsales_2.sales_price) * (tsales_2.discount/100))) * tsales_2.length) as total_bill')
        )
        ->groupBy(
            'tsales.Sal_inv_no',
            'tsales.sa_date',
            'acc_name.ac_name', // Fixed alias
            'tsales.pur_ord_no',
            'comp_acc.ac_name', // Fixed alias
            'tsales.company_name',
            'tsales.Sales_Remarks',
            'tsales.pur_against',
            'tsales.prefix',
            'tsales.ConvanceCharges',
            'tsales.LaborCharges',
            'tsales.Bill_discount',
            'tsales.Cash_name'
        )
        ->orderBy('tsales.Sal_inv_no', 'desc') // Order by latest date
        ->paginate(100); // Paginate (100 records per page)


        return view('sale2.index',compact('pur2'));
    }

    public function create(Request $request)
    {
        $items = Item_entry2::orderBy('item_name', 'asc')->get();
        $coa = AC::orderBy('ac_name', 'asc')->get();
        $item_group = Item_Groups::all();
        
        return view('sale2.create',compact('items','coa','item_group'));
    }

    public function store(Request $request)
    {
        
        $pur2 = new tsales();

        if ($request->has('sa_date') && $request->sa_date) {
            $pur2->sa_date=$request->sa_date;
        }
        if ($request->has('pur_ord_no') && $request->pur_ord_no) {
            $pur2->pur_ord_no=$request->pur_ord_no;
        }
        if ($request->has('sales_against') && $request->sales_against) {
            $pur2->pur_against=$request->sales_against;
        }
        if ($request->has('account_name') && $request->account_name) {
            $pur2->account_name=$request->account_name;
        }
        if ($request->has('disp_account_name') && $request->disp_account_name) {
            $pur2->company_name=$request->disp_account_name;
        }
        if ($request->has('sal_inv_no') && $request->sal_inv_no) {
            $pur2->pur_against=$request->sal_inv_no;
        }
        if ($request->has('Cash_pur_name') && $request->Cash_pur_name) {
            $pur2->Cash_name=$request->Cash_pur_name;
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

        $pur2->created_by = session('user_id');
        $pur2->save();

        $pur_2_id = tsales::latest()->first();
        
        if($request->has('items'))
        {
            for($i=0;$i<$request->items;$i++)
            {
                if(filled($request->item_name[$i]))
                {
                    $tsales_2 = new tsales_2();

                    $tsales_2->sales_inv_cod=$pur_2_id['Sal_inv_no'];
                    $tsales_2->item_cod=$request->item_cod[$i];

                    if ($request->remarks[$i]!=null) {
                        $tsales_2->remarks=$request->remarks[$i];
                    }
                    if ($request->pur2_qty2[$i]!=null) {
                        $tsales_2->Sales_qty2=$request->pur2_qty2[$i];
                    }
                    if ($request->pur2_per_unit[$i]!=null) {
                        $tsales_2->sales_price=$request->pur2_per_unit[$i];
                    }
                    if ($request->weight_per_piece[$i]!=null) {
                        $tsales_2->weight_pc=$request->weight_per_piece[$i];
                    }
                    if ($request->pur2_len[$i]!=null) {
                        $tsales_2->length=$request->pur2_len[$i];
                    }
                    if ($request->pur2_price_date[$i]!=null) {
                        $tsales_2->rat_dat=$request->pur2_price_date[$i];
                    }
                    if ($request->pur2_percentage[$i]!=null) {
                        $tsales_2->discount=$request->pur2_percentage[$i];
                    }
                    
                    $tsales_2->save();
                }
            }
        }

        if($request->hasFile('att')){
            $files = $request->file('att');
            foreach ($files as $file)
            {
                $pur2Att = new sale2_att();
                $pur2Att->sale2_id = $pur_2_id['Sal_inv_no'];
                $extension = $file->getClientOriginalExtension();
                $pur2Att->att_path = $this->sale2Doc($file,$extension);
                $pur2Att->save();
            }
        }

        // is induced 1 for From Stock Out
        if($request->has('isInduced') && $request->isInduced == 1){
            $tstock_out = new tstock_out();

            $SalinducedID=$pur_2_id['Sal_inv_no'];
            $prefix=$pur_2_id['prefix'];
            $pur_inv = $prefix.''.$SalinducedID;
            $tstock_out->pur_inv = $pur_inv;
            tstock_out::where('Sal_inv_no', $request->inducedID)->update([
                'pur_inv'=>$tstock_out->pur_inv,
            ]);
        }   

        // is induced 2 for From Stock Out
        elseif($request->has('isInduced') && $request->isInduced == 2){
            $tpurchase = new tpurchase();

            $SalinducedID=$pur_2_id['Sal_inv_no'];
            $prefix=$pur_2_id['prefix'];
            $sales_against = $prefix.''.$SalinducedID;
            $tpurchase->sales_against = $sales_against;
            tpurchase::where('Sale_inv_no', $request->inducedID)->update([
                'sales_against'=>$tpurchase->sales_against,
            ]);
        }

        // return redirect()->route('all-sale2invoices-paginate');

        return redirect()->route('show-sales2', $pur_2_id['Sal_inv_no']);

    }

    public function edit($id)
    {
        $item_group = Item_Groups::all();
        $items = Item_entry2::orderBy('item_name', 'asc')->get();
        $coa = AC::orderBy('ac_name', 'asc')->get();

        $pur2 = tsales::where('tsales.Sal_inv_no',$id)
        ->select(
            'tsales.Sal_inv_no','tsales.sa_date','tsales.pur_ord_no', 'tsales.company_name','tsales.Sales_Remarks','tsales.pur_against',
            'tsales.ConvanceCharges','tsales.cash_Pur_address','tsales.LaborCharges','tsales.Bill_discount','tsales.prefix','tsales.account_name',
            'tsales.company_name','tsales.Cash_name',
        )
        ->groupby('tsales.Sal_inv_no','tsales.sa_date','tsales.pur_ord_no','tsales.company_name',
        'tsales.Sales_Remarks','tsales.cash_Pur_address','tsales.pur_against','tsales.ConvanceCharges','tsales.account_name',
        'tsales.LaborCharges','tsales.Bill_discount','tsales.prefix','tsales.company_name','tsales.Cash_name')
        ->first();

        $pur2_item = tsales_2::where('tsales_2.sales_inv_cod',$id)->get();

        return view('sale2.edit',compact('pur2','pur2_item','items','coa','item_group'));
    }

    public function update(Request $request){

        $pur2 = tsales::where('Sal_inv_no',$request->pur2_id)->get()->first();

        if ($request->has('sa_date') && $request->sa_date) {
            $pur2->sa_date=$request->sa_date;
        }
        if ($request->has('pur_ord_no') && $request->pur_ord_no OR empty($request->pur_ord_no)) {
            $pur2->pur_ord_no=$request->pur_ord_no;
        }
        if ($request->has('sales_against') && $request->sales_against OR empty($request->sales_against)) {
            $pur2->pur_against=$request->sales_against;
        }
        if ($request->has('account_name') && $request->account_name) {
            $pur2->account_name=$request->account_name;
        }
        if ($request->has('disp_account_name') && $request->disp_account_name) {
            $pur2->company_name=$request->disp_account_name;
        }
        if ($request->has('Cash_pur_name') && $request->Cash_pur_name OR empty($request->Cash_pur_name)) {
            $pur2->Cash_name=$request->Cash_pur_name;
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

        tsales::where('Sal_inv_no', $request->pur2_id)->update([
            'sa_date'=>$pur2->sa_date,
            'pur_ord_no'=>$pur2->pur_ord_no,
            'pur_against'=>$pur2->pur_against,
            'account_name'=>$pur2->account_name,
            'company_name'=>$pur2->company_name,
            'Cash_name'=>$pur2->Cash_name,
            'cash_Pur_address'=>$pur2->cash_Pur_address,
            'Sales_Remarks'=>$pur2->Sales_Remarks,
            'ConvanceCharges'=>$pur2->ConvanceCharges,
            'LaborCharges'=>$pur2->LaborCharges,
            'Bill_discount'=>$pur2->Bill_discount,
            'updated_by' => session('user_id'),
        ]);

        tsales_2::where('sales_inv_cod', $request->pur2_id)->delete();

        if($request->has('items'))
        {
            for($i=0;$i<$request->items;$i++)
            {
                if(filled($request->item_name[$i]))
                {
                    $tsales_2 = new tsales_2();

                    $tsales_2->sales_inv_cod=$request->pur2_id;
                    $tsales_2->item_cod=$request->item_cod[$i];

                    if ($request->remarks[$i]!=null OR empty($request->remarks[$i])) {
                        $tsales_2->remarks=$request->remarks[$i];
                    }
                    if ($request->pur2_qty2[$i]!=null) {
                        $tsales_2->Sales_qty2=$request->pur2_qty2[$i];
                    }
                    if ($request->pur2_per_unit[$i]!=null) {
                        $tsales_2->sales_price=$request->pur2_per_unit[$i];
                    }
                    if ($request->weight_per_piece[$i]!=null) {
                        $tsales_2->weight_pc=$request->weight_per_piece[$i];
                    }
                    if ($request->pur2_len[$i]!=null) {
                        $tsales_2->length=$request->pur2_len[$i];
                    }
                    if ($request->pur2_price_date[$i]!=null) {
                        $tsales_2->rat_dat=$request->pur2_price_date[$i];
                    }
                    if ($request->pur2_percentage[$i]!=null) {
                        $tsales_2->discount=$request->pur2_percentage[$i];
                    }
                    
                    $tsales_2->save();
                }
            }
        }

        if($request->hasFile('att')){
            $files = $request->file('att');
            foreach ($files as $file)
            {
                $pur2Att = new sale2_att();
                $pur2Att->sale2_id = $request->pur2_id;
                $extension = $file->getClientOriginalExtension();
                $pur2Att->att_path = $this->sale2Doc($file,$extension);
                $pur2Att->save();
            }
        }

        return redirect()->route('show-sales2',$request->pur2_id);
    }


    public function updatebill(Request $request)
    {
        // Validate the request data
        $request->validate([
            'pur_ord_no' => 'required|string', // Ensure it's a string if it's a text input
            'pur3_id' => 'required|integer',   // Validate pur3_id to be a valid integer
        ]);
    
        // Get the new bill number
        $pur_ord_no = $request->pur_ord_no;
    
        // Update the record in tsales table
        tsales::where('Sal_inv_no', $request->pur3_id)
              ->update([
                  'pur_ord_no' => $pur_ord_no,
              ]);
    
        // Redirect to the appropriate route
        return redirect()->route('all-sale2invoices-paginate');
    }
    

    public function addAtt(Request $request)
    {
        $sale2_id=$request->att_id;

        if($request->hasFile('addAtt')){
            $files = $request->file('addAtt');
            foreach ($files as $file)
            {
                $sale2_att = new sale2_att();
                $sale2_att->created_by = session('user_id');
                $sale2_att->sale2_id = $sale2_id;
                $extension = $file->getClientOriginalExtension();
                $sale2_att->att_path = $this->sale2Doc($file,$extension);
                $sale2_att->save();
            }
        }
        return redirect()->route('all-sale2invoices-paginate');

    }

    public function destroy(Request $request)
    {
        tsales::where('Sal_inv_no', $request->delete_purc2)->update([
            'status' => '0',
            'updated_by' => session('user_id'),
        ]);
        return redirect()->route('all-sale2invoices-paginate');
    }

    public function show(string $id)
    {
        $pur = tsales::where('Sal_inv_no',$id)
                ->join('ac as acc_name','tsales.account_name','=','acc_name.ac_code')
                ->join('ac as dispt_to','tsales.company_name','=','dispt_to.ac_code')
                ->select('tsales.*','dispt_to.ac_name as disp_to','acc_name.ac_name as ac_name', 
                'acc_name.address as address', 'acc_name.phone_no as phone_no')
                ->first();

        $pur2 = tsales_2::where('sales_inv_cod',$id)
                ->join('item_entry2 as ie','tsales_2.item_cod','=','ie.it_cod')
                ->select('tsales_2.*','ie.item_name')
                ->get();

        return view('sale2.view',compact('pur','pur2'));
    }

    public function getAttachements(Request $request)
    {
        $sale2_atts = sale2_att::where('sale2_id', $request->id)->get();
        
        return $sale2_atts;
    }

    public function getunclosed()
    {
        $unclosed_inv = tsales::where(function ($query) {
            $query->where('pur_against', '')
                  ->orWhereNull('pur_against');
        })
        ->join('ac', 'ac.ac_code', '=', 'tsales.account_name')
        ->join('ac as dispt_acc', 'dispt_acc.ac_code', '=', 'tsales.company_name')
        ->select('tsales.*', 'ac.ac_name as acc_name','dispt_acc.ac_name as disp_acc')  // Select fields from both tables as needed
        ->get();
        return $unclosed_inv;
    }

    public function getItems($id){

        $pur1= tsales::where('Sal_inv_no',$id)->get()->first();

        $pur2 = tsales_2::where('sales_inv_cod',$id)
        ->join('item_entry as ie','tsales_2.item_cod','=','ie.it_cod')
        ->select('tsales_2.*','ie.item_name')
        ->get();

        return response()->json([
            'pur1' => $pur1,
            'pur2' => $pur2,
        ]);
    }

    public function deleteAtt($id)
    {
        $doc=sale2_att::where('att_id', $id)->select('att_path')->first();
        $filePath = public_path($doc['att_path']);
        if (File::exists($filePath)) {
            File::delete($filePath);
            $sale2_att = sale2_att::where('att_id', $id)->delete();
            return response()->json(['message' => 'File deleted successfully.']);
        } else {
            return response()->json(['message' => 'File not found.'], 404);
        }	
    }

    public function view($id)
    {
        $doc=sale2_att::where('att_id', $id)->select('att_path')->first();
        $filePath = public_path($doc['att_path']);
        if (file_exists($filePath)) {
            return Response::file($filePath);
        } 
    }

    public function downloadAtt($id)
    {
        $doc=sale2_att::where('att_id', $id)->select('att_path')->first();
        $filePath = public_path($doc['att_path']);
        if (file_exists($filePath)) {
            return Response::download($filePath);
        } 
    }

    public function showAllPDF($id)

    {
        $purchase = tsales::where('Sal_inv_no', $id)
        ->leftJoin('ac as account', 'account.ac_code', '=', 'tsales.account_name')
        ->leftJoin('ac as company', 'company.ac_code', '=', 'tsales.company_name')
        ->select('tsales.*', 'account.ac_name as ac_name', 'account.address as ac_add' , 'account.phone_no as ac_phone_no' ,'company.ac_name as company_name')
        ->first();


        $purchase_items = tsales_2::where('sales_inv_cod',$id)
                ->join('item_entry2','tsales_2.item_cod','=','item_entry2.it_cod')
                ->select('tsales_2.*','item_entry2.item_name')
                ->get();
                
        $pdf = new MyPDF();

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('MFI');
        $pdf->SetTitle('Sale Invoice-'.$purchase['prefix'].$purchase['Sal_inv_no']);
        $pdf->SetSubject('Sale Invoice-'.$purchase['prefix'].$purchase['Sal_inv_no']);
        $pdf->SetKeywords('Sale Invoice, TCPDF, PDF');
                   
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

        $heading='<h1 style="font-size:20px;text-align:center;font-style:italic;text-decoration:underline;color:#17365D">Sale Invoice</h1>';
        $pdf->writeHTML($heading, true, false, true, false, '');
        $pdf->writeHTML('<style>' . $margin_bottom . '</style>', true, false, true, false, '');

        $html = '<table style="margin-bottom:1rem">';
        $html .= '<tr>';
        $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Invoice No: &nbsp;<span style="text-decoration: underline;color:#000">'.$purchase['prefix'].$purchase['Sal_inv_no'].'</span></td>';
        $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Date: &nbsp;<span style="color:#000">'.\Carbon\Carbon::parse($purchase['sa_date'])->format('d-m-y').'</span></td>';
        $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Mill Inv No: <span style="text-decoration: underline;color:#000">'.$purchase['pur_ord_no'].'</span></td>';
        // $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Login: &nbsp; <span style="text-decoration: underline;color:#000">Hamza</span></td>';
        $html .= '</tr>';
        $html .= '</table>';

        // $pdf->writeHTML($html, true, false, true, false, '');

        $html .= '<table border="0.1px" style="border-collapse: collapse;">';
        $html .= '<tr>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Account Name </td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['ac_name'].'</td>';
        $html .= '<td width="20%" width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Name Of Person</td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['Cash_name'].'</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td width="20%" width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D" >Address </td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['ac_add'].'</td>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Company Name</td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['company_name'].'</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Phone </td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['ac_phone_no'].'</td>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Purchase Invoice#</td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['pur_against'].'</td>';
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
            $html .= '<td style="width:13%;border-right:1px dashed #000; text-align:center">' . round($amount, 2) . '</td>';
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
        $pdf->Output($purchase['ac_name'].'_'.$purchase['prefix'].$purchase['Sal_inv_no'].'.pdf', 'I');
    }
    

    public function noLengthPDF($id)
    {
        $purchase = tsales::where('Sal_inv_no', $id)
        ->leftJoin('ac as account', 'account.ac_code', '=', 'tsales.account_name')
        ->leftJoin('ac as company', 'company.ac_code', '=', 'tsales.company_name')
        ->select('tsales.*', 'account.ac_name as ac_name', 'account.address as ac_add' , 'account.phone_no as ac_phone_no' ,'company.ac_name as company_name')
        ->first();


        $purchase_items = tsales_2::where('sales_inv_cod',$id)
                ->join('item_entry2','tsales_2.item_cod','=','item_entry2.it_cod')
                ->select('tsales_2.*','item_entry2.item_name')
                ->get();
                
        $pdf = new MyPDF();

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('MFI');
        $pdf->SetTitle('Sale Invoice-'.$purchase['prefix'].$purchase['Sal_inv_no']);
        $pdf->SetSubject('Sale Invoice-'.$purchase['prefix'].$purchase['Sal_inv_no']);
        $pdf->SetKeywords('Sale Invoice, TCPDF, PDF');
                   
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

        $heading='<h1 style="font-size:20px;text-align:center;font-style:italic;text-decoration:underline;color:#17365D">Sale Invoice</h1>';
        $pdf->writeHTML($heading, true, false, true, false, '');
        $pdf->writeHTML('<style>' . $margin_bottom . '</style>', true, false, true, false, '');

        $html = '<table style="margin-bottom:1rem">';
        $html .= '<tr>';
        $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Invoice No: &nbsp;<span style="text-decoration: underline;color:#000">'.$purchase['prefix'].$purchase['Sal_inv_no'].'</span></td>';
        $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Date: &nbsp;<span style="color:#000">'.\Carbon\Carbon::parse($purchase['sa_date'])->format('d-m-y').'</span></td>';
        $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Mill Inv No: <span style="text-decoration: underline;color:#000">'.$purchase['pur_ord_no'].'</span></td>';
        // $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Login: &nbsp; <span style="text-decoration: underline;color:#000">Hamza</span></td>';
        $html .= '</tr>';
        $html .= '</table>';

        // $pdf->writeHTML($html, true, false, true, false, '');

        $html .= '<table border="0.1px" style="border-collapse: collapse;">';
        $html .= '<tr>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Account Name </td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['ac_name'].'</td>';
        $html .= '<td width="20%" width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Name Of Person</td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['Cash_name'].'</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td width="20%" width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D" >Address </td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['ac_add'].'</td>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Company Name</td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['company_name'].'</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Phone </td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['ac_phone_no'].'</td>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Purchase Invoice#</td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['pur_against'].'</td>';
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
        $html .= '<th style="width:28%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Item Name</th>';
        $html .= '<th style="width:21%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Description</th>';
        $html .= '<th style="width:10%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Qty</th>';
        $html .= '<th style="width:13%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">List Rate</th>';
        $html .= '<th style="width:7%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">%</th>';
        $html .= '<th style="width:15%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Amount</th>';
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
            $html .= '<td style="width:28%;border-right:1px dashed #000">' . $items['item_name'] . '</td>';
            $html .= '<td style="width:21%;border-right:1px dashed #000; font-size:9px;">' . $items['remarks'] . '</td>';
            $html .= '<td style="width:10%;border-right:1px dashed #000; text-align:center">' . $items['Sales_qty2'] . '</td>';
            $total_quantity += $items['Sales_qty2'];
             // Calculate the list price
             $price = $items['sales_price'] * $items['length'];
            $html .= '<td style="width:13%;border-right:1px dashed #000; text-align:center">' . $price . '</td>';
            $html .= '<td style="width:7%;border-right:1px dashed #000; text-align:center">' . $items['discount'] . '</td>';

            // Calculate the total weight and amount
            $total_weight += $items['Sales_qty2'] * $items['weight_pc'];
            $amount = (($items['Sales_qty2'] * $items['sales_price']) + (($items['Sales_qty2'] * $items['sales_price']) * ($items['discount'] / 100))) * $items['length'];
            $html .= '<td style="width:15%;border-right:1px dashed #000; text-align:center">' . round($amount, 2) . '</td>';
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
        $pdf->Output($purchase['ac_name'].'_'.$purchase['prefix'].$purchase['Sal_inv_no'].'.pdf', 'I');

    }

    public function onlyPriceQtyPDF($id)
    {
        $purchase = tsales::where('Sal_inv_no', $id)
        ->leftJoin('ac as account', 'account.ac_code', '=', 'tsales.account_name')
        ->leftJoin('ac as company', 'company.ac_code', '=', 'tsales.company_name')
        ->select('tsales.*', 'account.ac_name as ac_name', 'account.address as ac_add' , 'account.phone_no as ac_phone_no' ,'company.ac_name as company_name')
        ->first();


        $purchase_items = tsales_2::where('sales_inv_cod',$id)
                ->join('item_entry2','tsales_2.item_cod','=','item_entry2.it_cod')
                ->select('tsales_2.*','item_entry2.item_name')
                ->get();
                
        $pdf = new MyPDF();

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('MFI');
        $pdf->SetTitle('Sale Invoice-'.$purchase['prefix'].$purchase['Sal_inv_no']);
        $pdf->SetSubject('Sale Invoice-'.$purchase['prefix'].$purchase['Sal_inv_no']);
        $pdf->SetKeywords('Sale Invoice, TCPDF, PDF');
                   
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

        $heading='<h1 style="font-size:20px;text-align:center;font-style:italic;text-decoration:underline;color:#17365D">Sale Invoice</h1>';
        $pdf->writeHTML($heading, true, false, true, false, '');
        $pdf->writeHTML('<style>' . $margin_bottom . '</style>', true, false, true, false, '');

        $html = '<table style="margin-bottom:1rem">';
        $html .= '<tr>';
        $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Invoice No: &nbsp;<span style="text-decoration: underline;color:#000">'.$purchase['prefix'].$purchase['Sal_inv_no'].'</span></td>';
        $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Date: &nbsp;<span style="color:#000">'.\Carbon\Carbon::parse($purchase['sa_date'])->format('d-m-y').'</span></td>';
        $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Mill Inv No: <span style="text-decoration: underline;color:#000">'.$purchase['pur_ord_no'].'</span></td>';
        // $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Login: &nbsp; <span style="text-decoration: underline;color:#000">Hamza</span></td>';
        $html .= '</tr>';
        $html .= '</table>';

        // $pdf->writeHTML($html, true, false, true, false, '');

        $html .= '<table border="0.1px" style="border-collapse: collapse;">';
        $html .= '<tr>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Account Name </td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['ac_name'].'</td>';
        $html .= '<td width="20%" width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Name Of Person</td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['Cash_name'].'</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td width="20%" width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D" >Address </td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['ac_add'].'</td>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Company Name</td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['company_name'].'</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Phone </td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['ac_phone_no'].'</td>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Purchase Invoice#</td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['pur_against'].'</td>';
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
        $html .= '<th style="width:29%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Item Name</th>';
        $html .= '<th style="width:25%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Description</th>';
        $html .= '<th style="width:10%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Qty</th>';
        $html .= '<th style="width:14%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Price</th>';
        $html .= '<th style="width:16%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Amount</th>';
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
            $html .= '<td style="width:29%;border-right:1px dashed #000">' . $items['item_name'] . '</td>';
            $html .= '<td style="width:25%;border-right:1px dashed #000; font-size:9px;">' . $items['remarks'] . '</td>';
            $html .= '<td style="width:10%;border-right:1px dashed #000; text-align:center">' . $items['Sales_qty2'] . '</td>';
            $total_quantity += $items['Sales_qty2'];
             // Calculate the list price
             $price = (($items['sales_price']) + (( $items['sales_price']) * ($items['discount'] / 100))) * $items['length'];
            $html .= '<td style="width:14%;border-right:1px dashed #000; text-align:center">' . $price . '</td>';

            // Calculate the total weight and amount
            $total_weight += $items['Sales_qty2'] * $items['weight_pc'];
            $amount = (($items['Sales_qty2'] * $items['sales_price']) + (($items['Sales_qty2'] * $items['sales_price']) * ($items['discount'] / 100))) * $items['length'];
            $html .= '<td style="width:16%;border-right:1px dashed #000; text-align:center">' . round($amount, 2) . '</td>';
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
        $pdf->Output($purchase['ac_name'].'_'.$purchase['prefix'].$purchase['Sal_inv_no'].'.pdf', 'I');
    }

    public function generatePDF(Request $request)
    {
        if($request->print_type==1){
            $this->showAllPDF($request->print_sale2);
        }
        elseif($request->print_type==2){
            $this->noLengthPDF($request->print_sale2);
        }
        elseif($request->print_type==3){
            $this->onlyPriceQtyPDF($request->print_sale2);
        }
    }
}
