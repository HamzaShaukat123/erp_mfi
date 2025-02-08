<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use App\Traits\SaveImage;
use App\Models\AC;
use App\Models\Item_entry;
use App\Models\Sales;
use App\Models\Sales_2;
use App\Models\sale1_att;
use App\Services\myPDF;


class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use SaveImage;

    public function index()
    {
        $sales = Sales::where('sales.status', 1)
        ->leftjoin ('sales_2', 'sales_2.sales_inv_cod' , '=', 'sales.Sal_inv_no')
        ->join('ac','sales.account_name','=','ac.ac_code')
        ->select(
            'sales.Sal_inv_no','sales.sa_date','sales.Cash_pur_name','sales.Sales_remarks','ac.ac_name',
            'sales.pur_ord_no', 'sales.ConvanceCharges', 'sales.LaborCharges','sales.Bill_discount', 'sales.bill_not', 'sales.prefix',
            \DB::raw('SUM(sales_2.Sales_qty) as weight_sum'),
            \DB::raw('SUM(sales_2.Sales_qty*sales_2.sales_price) as total_bill'),
        )
        ->groupby('sales.Sal_inv_no','sales.sa_date','sales.Cash_pur_name','sales.Sales_remarks','ac.ac_name',
        'sales.pur_ord_no', 'sales.ConvanceCharges', 'sales.LaborCharges','sales.Bill_discount','sales.bill_not','sales.prefix' )
        ->get();

        return view('sales.index',compact('sales'));
    }



    public function indexPaginate()
    {
        $sales = Sales::where('sales.status', 1)
        ->leftJoin('sales_2', 'sales_2.sales_inv_cod', '=', 'sales.Sal_inv_no')
        ->join('ac', 'sales.account_name', '=', 'ac.ac_code')
        ->select(
            'sales.Sal_inv_no',
            'sales.sa_date',
            'sales.Cash_pur_name',
            'sales.Sales_remarks',
            'ac.ac_name',
            'sales.pur_ord_no',
            'sales.ConvanceCharges',
            'sales.LaborCharges',
            'sales.Bill_discount',
            'sales.bill_not',
            'sales.prefix',
            \DB::raw('SUM(sales_2.Sales_qty) as weight_sum'),
            \DB::raw('SUM(sales_2.Sales_qty * sales_2.sales_price) as total_bill') // Removed extra comma
        )
        ->groupBy(
            'sales.Sal_inv_no',
            'sales.sa_date',
            'sales.Cash_pur_name',
            'sales.Sales_remarks',
            'ac.ac_name',
            'sales.pur_ord_no',
            'sales.ConvanceCharges',
            'sales.LaborCharges',
            'sales.Bill_discount',
            'sales.bill_not',
            'sales.prefix'
        )
        ->orderBy('sales.Sal_inv_no', 'desc') // Get the latest records
        ->paginate(100); // Paginate (100 records per page)
    
    
        return view('sales.index',compact('sales'));
    }

    public function create(Request $request)
    {
        $items = Item_entry::orderBy('item_name', 'asc')->get();
        $coa = AC::orderBy('ac_name', 'asc')->get();

        return view('sales.create',compact('items','coa'));
    }

    public function store(Request $request)
    {
        $sales = new Sales();

        // $sales->Sal_inv_no;
        if ($request->has('date') && $request->date) {
            $sales->sa_date=$request->date;
        }
        if ($request->has('bill_no') && $request->bill_no) {
            $sales->pur_ord_no=$request->bill_no;
        }
        if ($request->has('remarks') && $request->remarks) {
            $sales->Sales_remarks=$request->remarks;
        }
        if ($request->has('labour_charges') && $request->labour_charges) {
            $sales->LaborCharges=$request->labour_charges;
        }
        if ($request->has('gst') && $request->gst) {
            $sales->Gst_sal=$request->gst;
        }
        if ($request->has('convance_charges') && $request->convance_charges) {
            $sales->ConvanceCharges=$request->convance_charges;
        }
        if ($request->has('nop') && $request->nop) {
            $sales->Cash_pur_name=$request->nop;
        }
        if ($request->has('address') && $request->address) {
            $sales->cash_Pur_address=$request->address;
        }
        if ($request->has('cash_pur_phone') && $request->cash_pur_phone) {
            $sales->cash_pur_phone=$request->cash_pur_phone;
        }
        if ($request->has('bill_discount') && $request->bill_discount) {
            $sales->Bill_discount=$request->bill_discount;
        }
        if ($request->has('account_name') && $request->account_name) {
            $sales->account_name=$request->account_name;
        }
        if ($request->has('bill_status') && $request->bill_status) {
            $sales->bill_not=$request->bill_status;
        }
        // if ($request->has('totalAmount') && $request->totalAmount) {
        //     $sales->sed_sal=$request->totalAmount;
        // }

        $sales->created_by = session('user_id');
        $sales->status=1;

        $sales->save();

        $latest_invoice = Sales::latest()->first();
        $invoice_id = $latest_invoice['Sal_inv_no'];

        if($request->has('items'))
        {
            for($i=0;$i<$request->items;$i++)
            {
                if(filled($request->item_code[$i]))
                {
                    $sales_2 = new Sales_2();
                    $sales_2->sales_inv_cod=$invoice_id;
                    $sales_2->item_cod=$request->item_code[$i];
                    $sales_2->remarks=$request->item_remarks[$i];
                    $sales_2->Sales_qty2=$request->item_qty[$i];
                    $sales_2->sales_price=$request->item_price[$i];
                    $sales_2->Sales_qty=$request->item_weight[$i];
    
                    $sales_2->save();
                }
            }
        }

        if($request->hasFile('att')){
            $files = $request->file('att');
            foreach ($files as $file)
            {
                $sale1_att = new sale1_att();
                $sale1_att->sale1_id = $invoice_id;
                $extension = $file->getClientOriginalExtension();
                $sale1_att->att_path = $this->sale1Doc($file,$extension);
                $sale1_att->save();
            }
        }
        return redirect()->route('all-saleinvoices-paginate');
    }

    public function showNew(string $id)
    {
        $sales = Sales::where('Sal_inv_no',$id)
                        ->join('ac','sales.account_name','=','ac.ac_code')
                        ->first();
        $sale_items = Sales_2::where('sales_inv_cod',$id)
                        ->join('item_entry','sales_2.item_cod','=','item_entry.it_cod')
                        ->select('sales_2.*','item_entry.item_name')
                        ->get();
        
        return view('sales.view',compact('sales','sale_items'));
    }

    public function edit($id)
    {
        $sales = Sales::where('Sal_inv_no',$id)->first();
        $sale_items = Sales_2::where('sales_inv_cod',$id)->get();
        $sale_item_count=count($sale_items);
        $items = Item_entry::all();
        $coa = AC::all();
        return view('sales.edit', compact('sales','sale_items','items','coa','sale_item_count'));
    }

    public function update(Request $request)
    {
        $sale1 = Sales::where('Sal_inv_no',$request->invoice_no)->get()->first();

        if ($request->has('date') && $request->date) {
            $sale1->sa_date=$request->date;
        }
        if ($request->has('bill_no') && $request->bill_no OR empty($request->bill_no)) {
            $sale1->pur_ord_no=$request->bill_no;
        }
        if ($request->has('remarks') && $request->remarks OR empty($request->remarks)) {
            $sale1->Sales_remarks=$request->remarks;
        }
        if ($request->has('labour_charges') && $request->labour_charges OR $request->labour_charges==0) {
            $sale1->LaborCharges=$request->labour_charges;
        }
        if ($request->has('convance_charges') && $request->convance_charges OR $request->convance_charges==0) {
            $sale1->ConvanceCharges=$request->convance_charges;
        }
        if ($request->has('nop') && $request->nop OR empty($request->nop)) {
            $sale1->Cash_pur_name=$request->nop;
        }
        if ($request->has('address') && $request->address OR empty($request->address))  {
            $sale1->cash_Pur_address=$request->address;
        }
        if ($request->has('cash_pur_phone') && $request->cash_pur_phone OR empty($request->cash_pur_phone)) {
            $sale1->cash_pur_phone=$request->cash_pur_phone;
        }
        if ($request->has('bill_discount') && $request->bill_discount OR $request->bill_discount==0) {
            $sale1->Bill_discount=$request->bill_discount;
        }
        if ($request->has('account_name') && $request->account_name) {
            $sale1->account_name=$request->account_name;
        }
        if ($request->has('bill_status') && $request->bill_status OR $request->bill_status==0) {
            $sale1->bill_not=$request->bill_status;
        }
        if ($request->has('totalAmount') && $request->totalAmount) {
            $sale1->sed_sal=$request->totalAmount;
        }
        Sales::where('Sal_inv_no', $request->invoice_no)->update([
            'sed_sal'=>$sale1->sed_sal,
            'bill_not'=>$sale1->bill_not,
            'account_name'=>$sale1->account_name,
            'Bill_discount'=>$sale1->Bill_discount,
            'cash_pur_phone'=>$sale1->cash_pur_phone,
            'cash_Pur_address'=>$sale1->cash_Pur_address,
            'Cash_pur_name'=>$sale1->Cash_pur_name,
            'ConvanceCharges'=>$sale1->ConvanceCharges,
            'LaborCharges'=>$sale1->LaborCharges,
            'Sales_remarks'=>$sale1->Sales_remarks,
            'sa_date'=>$sale1->sa_date,
            'pur_ord_no'=>$sale1->pur_ord_no,
            'updated_by' => session('user_id'),
        ]);
        
        Sales_2::where('sales_inv_cod', $request->invoice_no)->delete();
        
        if($request->has('items'))
        {
            for($i=0;$i<$request->items;$i++)
            {

                if(filled($request->item_code[$i]))
                {
                    $sales_2 = new Sales_2();
                    $sales_2->sales_inv_cod=$request->invoice_no;
                    $sales_2->item_cod=$request->item_code[$i];
                    if ($request->item_remarks[$i]!=null OR empty($request->item_remarks[$i])) {
                        $sales_2->remarks=$request->item_remarks[$i];
                    }
                    $sales_2->Sales_qty2=$request->item_qty[$i];
                    $sales_2->sales_price=$request->item_price[$i];
                    $sales_2->Sales_qty=$request->item_weight[$i];
                    $sales_2->save();
                }
            }
        }

        if($request->hasFile('att')){
            $files = $request->file('att');
            foreach ($files as $file)
            {
                $sale1_att = new sale1_att();
                $sale1_att->sale1_id = $request->invoice_no;
                $extension = $file->getClientOriginalExtension();
                $sale1_att->att_path = $this->sale1Doc($file,$extension);
                $sale1_att->save();
            }
        }

        return redirect()->route('all-saleinvoices-paginate');
    }

    public function addAtt(Request $request)
    {
        $sale1_id=$request->att_id;

        if($request->hasFile('addAtt')){
            $files = $request->file('addAtt');
            foreach ($files as $file)
            {
                $sale1_att = new sale1_att();
                $sale1_att->created_by = session('user_id');
                $sale1_att->sale1_id = $sale1_id;
                $extension = $file->getClientOriginalExtension();
                $sale1_att->att_path = $this->sale1Doc($file,$extension);
                $sale1_att->save();
            }
        }
        return redirect()->route('all-saleinvoices-paginate');
    }

    public function destroy(Request $request)
    {
        $sales = Sales::where('Sal_inv_no', $request->invoice_id)->update([
            'status' => '0',
            'updated_by' => session('user_id'),
        ]);
        return redirect()->route('all-saleinvoices-paginate');
    }

    public function deleteAtt($id)
    {
        $doc=sale1_att::where('att_id', $id)->select('att_path')->first();
        $filePath = public_path($doc['att_path']);
        if (File::exists($filePath)) {
            File::delete($filePath);
            $sale1_att = sale1_att::where('att_id', $id)->delete();
            return response()->json(['message' => 'File deleted successfully.']);
        } else {
            return response()->json(['message' => 'File not found.'], 404);
        }	
    }

    public function view($id)
    {
        $doc=sale1_att::where('att_id', $id)->select('att_path')->first();
        $filePath = public_path($doc['att_path']);
        if (file_exists($filePath)) {
            return Response::file($filePath);
        } 
    }

    public function downloadAtt($id)
    {
        $doc=sale1_att::where('att_id', $id)->select('att_path')->first();
        $filePath = public_path($doc['att_path']);
        if (file_exists($filePath)) {
            return Response::download($filePath);
        } 
    }


    public function getAttachements(Request $request)
    {
        $sale1_att = sale1_att::where('sale1_id', $request->id)->get();
        
        return $sale1_att;
 
    }

    public function generatePDF($id)
    {
        $sales = Sales::where('Sal_inv_no',$id)
        ->join('ac','sales.account_name','=','ac.ac_code')
        ->first();

        $sale_items = Sales_2::where('sales_inv_cod',$id)
                ->join('item_entry','sales_2.item_cod','=','item_entry.it_cod')
                ->select('sales_2.*','item_entry.item_name')
                ->get();

        $pdf = new MyPDF();

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('MFI');
        $pdf->SetTitle('Sale Invoice-'.$sales['prefix'].$sales['Sal_inv_no']);
        $pdf->SetSubject('Sale Invoice-'.$sales['prefix'].$sales['Sal_inv_no']);
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
        $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Invoice No: &nbsp;<span style="text-decoration: underline;color:#000">'.$sales['prefix'].$sales['Sal_inv_no'].'</span></td>';
        $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Date: &nbsp;<span style="color:#000">'.\Carbon\Carbon::parse($sales['sa_date'])->format('d-m-y').'</span></td>';
        $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Bill No: <span style="text-decoration: underline;color:#000">'.$sales['pur_ord_no'].'</span></td>';
        // $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Login: &nbsp; <span style="text-decoration: underline;color:#000">Hamza</span></td>';
        $html .= '</tr>';
        $html .= '</table>';

        // $pdf->writeHTML($html, true, false, true, false, '');

        $html .= '<table border="0.1px" style="border-collapse: collapse;">';
        $html .= '<tr>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Account Name </td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$sales['ac_name'].'</td>';
        $html .= '<td width="20%" width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Name Of Person</td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$sales['Cash_pur_name'].'</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td width="20%" width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D" >Address </td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$sales['address'].'</td>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Persons Address</td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$sales['cash_Pur_address'].'</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Phone </td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$sales['phone_no'].'</td>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Persons Phone</td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$sales['cash_pur_phone'].'</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Remarks </td>';
        $html .= '<td width="80%" style="font-size:10px;font-family:poppins;">'.$sales['Sales_remarks'].'</td>';
        $html .= '</tr>';
        $html .= '</table>';
        
        $pdf->writeHTML($html, true, false, true, false, '');
    
        $html = '<table border="0.3" style="text-align:center;margin-top:10px" >';
        $html .= '<tr>';
        $html .= '<th style="width:6%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">S/R</th>';
        $html .= '<th style="width:8%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Qty</th>';
        $html .= '<th style="width:26%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Item Name</th>';
        $html .= '<th style="width:24%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Description</th>';
        $html .= '<th style="width:12%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Price</th>';
        $html .= '<th style="width:12%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Weight</th>';
        $html .= '<th style="width:12%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Amount</th>';
        $html .= '</tr>';
        $html .= '</table>';

        $pdf->setTableHtml($html);

        $count=1;
        $total_weight=0;
        $total_quantity=0;
        $total_amount=0;
        $net_amount=0;

        $html .= '<table cellspacing="0" cellpadding="5">';
        foreach ($sale_items as $items) {
            if($count%2==0)
            {
                $html .= '<tr style="background-color:#f1f1f1">';
                $html .= '<td style="width:6%;border-right:0.3px dashed #000;border-left:0.3px dashed #000;font-size:10px;font-family:poppins;text-align:center;">'.$count.'</td>';
                $html .= '<td style="width:8%;border-right:0.3px dashed #000;font-size:10px;font-family:poppins;text-align:center">'.$items['Sales_qty2'].'</td>';
                $total_quantity=$total_quantity+$items['Sales_qty2'];
                $html .= '<td style="width:26%;border-right:0.3px dashed #000;font-size:10px;font-family:poppins;">'.$items['item_name'].'</td>';
                $html .= '<td style="width:24%;border-right:0.3px dashed #000;font-size:10px;font-family:poppins;">'.$items['remarks'].'</td>';
                $html .= '<td style="width:12%;border-right:0.3px dashed #000;font-size:10px;font-family:poppins;text-align:center">'.$items['sales_price'].'</td>';
                $html .= '<td style="width:12%;border-right:0.3px dashed #000;font-size:10px;font-family:poppins;text-align:center">'.$items['Sales_qty'].'</td>';
                $total_weight=$total_weight+$items['Sales_qty'];
                $html .= '<td style="width:12%;border-right:0.3px dashed #000;font-size:10px;font-family:poppins;">'.$items['Sales_qty']*$items['sales_price'].'</td>';
                $total_amount=$total_amount+($items['Sales_qty']*$items['sales_price']);
                $html .= '</tr>';
            }
            else{
                $html .= '<tr>';
                $html .= '<td style="width:6%;border-right:0.3px dashed #000;border-left:0.3px dashed #000;font-size:10px;font-family:poppins;text-align:center">'.$count.'</td>';
                $html .= '<td style="width:8%;border-right:0.3px dashed #000;font-size:10px;font-family:poppins;text-align:center">'.$items['Sales_qty2'].'</td>';
                $total_quantity=$total_quantity+$items['Sales_qty2'];
                $html .= '<td style="width:26%;border-right:0.3px dashed #000;font-size:10px;font-family:poppins;">'.$items['item_name'].'</td>';
                $html .= '<td style="width:24%;border-right:0.3px dashed #000;font-size:10px;font-family:poppins;">'.$items['remarks'].'</td>';
                $html .= '<td style="width:12%;border-right:0.3px dashed #000;font-size:10px;font-family:poppins;text-align:center">'.$items['sales_price'].'</td>';
                $html .= '<td style="width:12%;border-right:0.3px dashed #000;font-size:10px;font-family:poppins;text-align:center">'.$items['Sales_qty'].'</td>';
                $total_weight=$total_weight+$items['Sales_qty'];
                $html .= '<td style="width:12%;border-right:0.3px dashed #000;font-size:10px;font-family:poppins;">'.$items['Sales_qty']*$items['sales_price'].'</td>';
                $total_amount=$total_amount+($items['Sales_qty']*$items['sales_price']);
                $html .= '</tr>';
            }
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

        $roundedTotal= round($total_amount+$sales['LaborCharges']+$sales['ConvanceCharges']-$sales['Bill_discount']);
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
        $pdf->Cell(35, 5, $sales['LaborCharges'], 1, 'R');
        $pdf->SetXY(165, $currentY+13.7);
        $pdf->Cell(35, 5, $sales['ConvanceCharges'], 1, 'R');
        $pdf->SetXY(165, $currentY+20.5);
        $pdf->Cell(35, 5, $sales['Bill_discount'], 1, 'R');
        $pdf->SetXY(165, $currentY+27.3);
        $net_amount=number_format(round($total_amount+$sales['LaborCharges']+$sales['ConvanceCharges']-$sales['Bill_discount']));
        $pdf->SetFont('helvetica','B', 12);
        $pdf->Cell(35, 5,  $net_amount, 1, 'R');
        
        $pdf->SetFont('helvetica','BIU', 14);
        $pdf->SetTextColor(23, 54, 93);

        $pdf->SetXY(10, $currentY+20);
        $width = 100;
        $pdf->MultiCell($width, 10, $num_to_words, 0, 'L', 0, 1, '', '', true);
        $pdf->SetFont('helvetica','', 10);
        
        // Close and output PDF
        $pdf->Output('Sale Invoice_'.$sales['prefix'].$sales['Sal_inv_no'].'.pdf', 'I');
    }


}

