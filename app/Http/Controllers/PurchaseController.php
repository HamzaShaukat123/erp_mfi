<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item_entry;
use App\Models\AC;
use App\Models\purchase;
use App\Models\purchase_2;
use App\Models\pur1_att;
use Illuminate\Support\Facades\File;
use App\Traits\SaveImage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use App\Services\myPDF;

class PurchaseController extends Controller
{
    //
    use SaveImage;

    public function index()
    {
        $pur1 = purchase::where('purchase.status',1)
        ->leftjoin ('purchase_2', 'purchase_2.pur_cod' , '=', 'purchase.pur_id')
        ->join('ac', 'ac.ac_code', '=', 'purchase.ac_cod')
        ->select(
            'purchase.pur_id','purchase.pur_date','purchase.cash_saler_name','purchase.pur_remarks','ac.ac_name',
            'pur_bill_no', 'purchase.pur_convance_char', 'purchase.pur_labor_char','purchase.pur_discount','purchase.prefix',
            'purchase.sale_against',
            \DB::raw('SUM(purchase_2.pur_qty) as weight_sum'),
            \DB::raw('SUM(purchase_2.pur_qty*purchase_2.pur_price) as total_bill'),
        )
        ->groupby('purchase.pur_id','purchase.pur_date','purchase.cash_saler_name','purchase.pur_remarks','ac.ac_name',
        'pur_bill_no','purchase.pur_convance_char', 'purchase.sale_against', 'purchase.pur_labor_char','purchase.pur_discount','purchase.prefix')
        ->get();
        
        return view('purchase1.index',compact('pur1'));


     
    }


    public function UpdateSalesAgainst(Request $request)
    {
        // Validate the request data
        $request->validate([
            'sale_against' => 'required|string', // Ensure it's a string if it's a text input
            'pur3_id' => 'required|integer',   // Validate pur3_id to be a valid integer
        ]);
    
        // Get the new bill number
        $sale_against = $request->sale_against;
    
        // Update the record in purchase table
        purchase::where('pur_id', $request->pur3_id)
              ->update([
                  'sale_against' => $sale_against,
              ]);
    
        // Redirect to the appropriate route
        return redirect()->route('all-purchases1');
    }


    public function create(Request $request)
    {
        $items = Item_entry::orderBy('item_name', 'asc')->get();
        $coa = AC::orderBy('ac_name', 'asc')->get();

        return view('purchase1.create',compact('items','coa'));
    }

    public function store(Request $request)
    {
        $pur1 = new purchase();
        $pur1->created_by = session('user_id');

        if ($request->has('pur_date') && $request->pur_date) {
            $pur1->pur_date=$request->pur_date;
        }
        if ($request->has('pur_bill_no') && $request->pur_bill_no) {
            $pur1->pur_bill_no=$request->pur_bill_no;
        }
        if ($request->has('pur_sale_inv') && $request->pur_sale_inv) {
            $pur1->sale_against=$request->pur_sale_inv;
        }
        if ($request->has('ac_cod') && $request->ac_cod) {
            $pur1->ac_cod=$request->ac_cod;
        }
        if ($request->has('cash_saler_name') && $request->cash_saler_name) {
            $pur1->cash_saler_name=$request->cash_saler_name;
        }
        if ($request->has('cash_saler_address') && $request->cash_saler_address) {
            $pur1->cash_saler_address=$request->cash_saler_address;
        }
        if ($request->has('pur_remarks') && $request->pur_remarks) {
            $pur1->pur_remarks=$request->pur_remarks;
        }
        if ($request->has('pur_convance_char') && $request->pur_convance_char) {
            $pur1->pur_convance_char=$request->pur_convance_char;
        }
        if ($request->has('pur_labor_char') && $request->pur_labor_char) {
            $pur1->pur_labor_char=$request->pur_labor_char;
        }
        if ($request->has('bill_discount') && $request->bill_discount) {
            $pur1->pur_discount=$request->bill_discount;
        }

        $pur1->save();

        $pur_1_id = purchase::latest()->first();

        if($request->has('items'))
        {
            for($i=0;$i<$request->items;$i++)
            {

                if(filled($request->item_name[$i]))
                {
                    $purchase_2 = new purchase_2();

                    $purchase_2->pur_cod=$pur_1_id['pur_id'];
                    $purchase_2->item_cod=$request->item_cod[$i];
                    if ($request->remarks[$i]!=null) {
                        $purchase_2->remarks=$request->remarks[$i];
                    }
                    if ($request->pur_qty[$i]!=null) {
                        $purchase_2->pur_qty=$request->pur_qty[$i];
                    }
                    if ($request->pur_price[$i]!=null) {
                        $purchase_2->pur_price=$request->pur_price[$i];
                    }
                    if ($request->pur_qty2[$i]!=null) {
                        $purchase_2->pur_qty2=$request->pur_qty2[$i];
                    }
                    $purchase_2->save();
                }
            }
        }

        if($request->hasFile('att')){
            $files = $request->file('att');
            foreach ($files as $file)
            {
                $purAtt = new pur1_att();
                $purAtt->pur1_id = $pur_1_id['pur_id'];
                $extension = $file->getClientOriginalExtension();
                $purAtt->att_path = $this->pur1Doc($file,$extension);
                $purAtt->save();
            }
        }

        return redirect()->route('all-purchases1');
    }

    public function edit($id)
    {
        $items = Item_entry::all();
        $acc = AC::all();
        $pur = purchase::where('purchase.pur_id',$id)->first();
        $pur_items = purchase_2::where('purchase_2.pur_cod',$id)->get();

        return view('purchase1.edit',compact('items','acc','pur','pur_items'));
    }

    public function update(Request $request){

        $pur1 = purchase::where('pur_id',$request->pur_id)->get()->first();

        if ($request->has('pur_date') && $request->pur_date) {
            $pur1->pur_date=$request->pur_date;
        }

        if ($request->has('pur_bill_no') && $request->pur_bill_no OR empty($request->pur_bill_no)) {
            $pur1->pur_bill_no=$request->pur_bill_no;
        }
        if ($request->has('pur_sale_inv') && $request->pur_sale_inv OR empty($request->pur_sale_inv)) {
            $pur1->sale_against=$request->pur_sale_inv;
        }
        if ($request->has('ac_cod') && $request->ac_cod) {
            $pur1->ac_cod=$request->ac_cod;
        }
        if ($request->has('cash_saler_name') && $request->cash_saler_name OR empty($request->cash_saler_name)) {
            $pur1->cash_saler_name=$request->cash_saler_name;
        }
        if ($request->has('cash_saler_address') && $request->cash_saler_address OR empty($request->cash_saler_address)) {
            $pur1->cash_saler_address=$request->cash_saler_address;
        }
        if ($request->has('pur_remarks') && $request->pur_remarks OR empty($request->pur_remarks) ) {
            $pur1->pur_remarks=$request->pur_remarks;
        }
        if ($request->has('pur_convance_char') && $request->pur_convance_char OR $request->pur_convance_char==0) {
            $pur1->pur_convance_char=$request->pur_convance_char;
        }
        if ($request->has('pur_labor_char') && $request->pur_labor_char OR $request->pur_labor_char==0) {
            $pur1->pur_labor_char=$request->pur_labor_char;
        }
        if ($request->has('bill_discount') && $request->bill_discount OR $request->bill_discount==0) {
            $pur1->pur_discount=$request->bill_discount;
        }

        purchase::where('pur_id', $request->pur_id)->update([
            'pur_date'=>$pur1->pur_date,
            'pur_bill_no'=>$pur1->pur_bill_no,
            'sale_against'=>$pur1->sale_against,
            'ac_cod'=>$pur1->ac_cod,
            'cash_saler_name'=>$pur1->cash_saler_name,
            'cash_saler_address'=>$pur1->cash_saler_address,
            'pur_remarks'=>$pur1->pur_remarks,
            'pur_convance_char'=>$pur1->pur_convance_char,
            'pur_labor_char'=>$pur1->pur_labor_char,
            'pur_discount'=>$pur1->pur_discount,
            'updated_by' => session('user_id'),
        ]);

        purchase_2::where('pur_cod', $request->pur_id)->delete();

        if($request->has('items'))
        {
            for($i=0;$i<$request->items;$i++)
            {

                if(filled($request->item_name[$i]))
                {
                    $purchase_2 = new purchase_2();

                    $purchase_2->pur_cod=$request->pur_id;
                    $purchase_2->item_cod=$request->item_cod[$i];
                    if ($request->remarks[$i]!=null OR empty($request->remarks[$i])) {
                        $purchase_2->remarks=$request->remarks[$i];
                    }
                    if ($request->pur_qty[$i]!=null) {
                        $purchase_2->pur_qty=$request->pur_qty[$i];
                    }
                    if ($request->pur_price[$i]!=null) {
                        $purchase_2->pur_price=$request->pur_price[$i];
                    }
                    if ($request->pur_qty2[$i]!=null) {
                        $purchase_2->pur_qty2=$request->pur_qty2[$i];
                    }
                    $purchase_2->save();
                }
            }
        }

        if($request->hasFile('att')){
            $files = $request->file('att');
            foreach ($files as $file)
            {
                $purAtt = new pur1_att();
                $purAtt->pur1_id = $request->pur_id;
                $extension = $file->getClientOriginalExtension();
                $purAtt->att_path = $this->pur1Doc($file,$extension);
                $purAtt->save();
            }
        }

        return redirect()->route('all-purchases1');
    }

    public function addAtt(Request $request)
    {
        $pur_id=$request->att_id;

        if($request->hasFile('addAtt')){
            $files = $request->file('addAtt');
            foreach ($files as $file)
            {
                $purAtt = new pur1_att();
                $purAtt->created_by = session('user_id');
                $purAtt->pur1_id = $pur_id;
                $extension = $file->getClientOriginalExtension();
                $purAtt->att_path = $this->pur1Doc($file,$extension);
                $purAtt->save();
            }

        }
        return redirect()->route('all-purchases1');

    }

    public function destroy(Request $request)
    {
        $purc1 = purchase::where('pur_id', $request->delete_purc1)->update([
            'status' => '0',
            'updated_by' => session('user_id'),
        ]);
        return redirect()->route('all-purchases1');
    }

    public function show(string $id)
    {
        $pur = purchase::where('pur_id',$id)
                ->join('ac','purchase.ac_cod','=','ac.ac_code')
                ->first();

        $pur2 = purchase_2::where('pur_cod',$id)
                ->join('item_entry','purchase_2.item_cod','=','item_entry.it_cod')
                ->get();

        return view('purchase1.view',compact('pur','pur2'));
    }

    public function getAttachements(Request $request)
    {
        $pur1_atts = pur1_att::where('pur1_id', $request->id)->get();
        
        return $pur1_atts;
    }

    public function deleteAtt($id)
    {
        $doc=pur1_att::where('att_id', $id)->select('att_path')->first();
        $filePath = public_path($doc['att_path']);
        if (File::exists($filePath)) {
            File::delete($filePath);
            $pur1_att = pur1_att::where('att_id', $id)->delete();
            return response()->json(['message' => 'File deleted successfully.']);
        } else {
            return response()->json(['message' => 'File not found.'], 404);
        }	
    }

    public function view($id)
    {
        $doc=pur1_att::where('att_id', $id)->select('att_path')->first();
        $filePath = public_path($doc['att_path']);
        if (file_exists($filePath)) {
            return Response::file($filePath);
        } 
    }

    public function downloadAtt($id)
    {
        $doc=pur1_att::where('att_id', $id)->select('att_path')->first();
        $filePath = public_path($doc['att_path']);
        if (file_exists($filePath)) {
            return Response::download($filePath);
        } 
    }

    public function generatePDF($id)
    {
        $purchase = purchase::where('pur_id',$id)
        ->join('ac','purchase.ac_cod','=','ac.ac_code')
        ->first();

        $purchase_items = purchase_2::where('pur_cod',$id)
                ->join('item_entry','purchase_2.item_cod','=','item_entry.it_cod')
                ->get();

        $pdf = new MyPDF();

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('MFI');
        $pdf->SetTitle('Purchase Invoice-'.$purchase['prefix'].$purchase['pur_id']);
        $pdf->SetSubject('Purchase Invoice-'.$purchase['prefix'].$purchase['pur_id']);
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
        $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Invoice No: &nbsp;<span style="text-decoration: underline;color:#000">'.$purchase['prefix'].$purchase['pur_id'].'</span></td>';
        $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Date: &nbsp;<span style="color:#000">'.\Carbon\Carbon::parse($purchase['pur_date'])->format('d-m-y').'</span></td>';
        $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Mill Inv No: <span style="text-decoration: underline;color:#000">'.$purchase['pur_bill_no'].'</span></td>';
        // $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Login: &nbsp; <span style="text-decoration: underline;color:#000">Hamza</span></td>';
        $html .= '</tr>';
        $html .= '</table>';

        // $pdf->writeHTML($html, true, false, true, false, '');

        $html .= '<table border="0.1px" style="border-collapse: collapse;">';
        $html .= '<tr>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Account Name </td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['ac_name'].'</td>';
        $html .= '<td width="20%" width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Name Of Person</td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['cash_saler_name'].'</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td width="20%" width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D" >Address </td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['address'].'</td>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Persons Address</td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['cash_saler_address'].'</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Phone </td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['phone_no'].'</td>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Persons Phone</td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['cash_pur_phone'].'</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Remarks </td>';
        $html .= '<td width="80%" style="font-size:10px;font-family:poppins;">'.$purchase['pur_remarks'].'</td>';
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
        foreach ($purchase_items as $items) {
            if($count%2==0)
            {
                $html .= '<tr style="background-color:#f1f1f1">';
                $html .= '<td style="width:6%;border-right:0.3px dashed #000;border-left:0.3px dashed #000;font-size:10px;font-family:poppins;text-align:center;">'.$count.'</td>';
                $html .= '<td style="width:8%;border-right:0.3px dashed #000;font-size:10px;font-family:poppins;text-align:center">'.$items['pur_qty2'].'</td>';
                $total_quantity=$total_quantity+$items['pur_qty2'];
                $html .= '<td style="width:26%;border-right:0.3px dashed #000;font-size:10px;font-family:poppins;">'.$items['item_name'].'</td>';
                $html .= '<td style="width:24%;border-right:0.3px dashed #000;font-size:10px;font-family:poppins;">'.$items['remarks'].'</td>';
                $html .= '<td style="width:12%;border-right:0.3px dashed #000;font-size:10px;font-family:poppins;text-align:center">'.$items['pur_price'].'</td>';
                $html .= '<td style="width:12%;border-right:0.3px dashed #000;font-size:10px;font-family:poppins;text-align:center">'.$items['pur_qty'].'</td>';
                $total_weight=$total_weight+$items['pur_qty'];
                $html .= '<td style="width:12%;border-right:0.3px dashed #000;font-size:10px;font-family:poppins;">'.$items['pur_qty']*$items['pur_price'].'</td>';
                $total_amount=$total_amount+($items['pur_qty']*$items['pur_price']);
                $html .= '</tr>';
            }
            else{
                $html .= '<tr>';
                $html .= '<td style="width:6%;border-right:0.3px dashed #000;border-left:0.3px dashed #000;font-size:10px;font-family:poppins;text-align:center">'.$count.'</td>';
                $html .= '<td style="width:8%;border-right:0.3px dashed #000;font-size:10px;font-family:poppins;text-align:center">'.$items['pur_qty2'].'</td>';
                $total_quantity=$total_quantity+$items['pur_qty2'];
                $html .= '<td style="width:26%;border-right:0.3px dashed #000;font-size:10px;font-family:poppins;">'.$items['item_name'].'</td>';
                $html .= '<td style="width:24%;border-right:0.3px dashed #000;font-size:10px;font-family:poppins;">'.$items['remarks'].'</td>';
                $html .= '<td style="width:12%;border-right:0.3px dashed #000;font-size:10px;font-family:poppins;text-align:center">'.$items['pur_price'].'</td>';
                $html .= '<td style="width:12%;border-right:0.3px dashed #000;font-size:10px;font-family:poppins;text-align:center">'.$items['pur_qty'].'</td>';
                $total_weight=$total_weight+$items['pur_qty'];
                $html .= '<td style="width:12%;border-right:0.3px dashed #000;font-size:10px;font-family:poppins;">'.$items['pur_qty']*$items['pur_price'].'</td>';
                $total_amount=$total_amount+($items['pur_qty']*$items['pur_price']);
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

        $roundedTotal= round($total_amount+$purchase['pur_labor_char']+$purchase['pur_convance_char']-$purchase['pur_discount']);
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
        $pdf->Cell(35, 5, $purchase['pur_labor_char'], 1, 'R');
        $pdf->SetXY(165, $currentY+13.7);
        $pdf->Cell(35, 5, $purchase['pur_convance_char'], 1, 'R');
        $pdf->SetXY(165, $currentY+20.5);
        $pdf->Cell(35, 5, $purchase['pur_discount'], 1, 'R');
        $pdf->SetXY(165, $currentY+27.3);
        $net_amount=number_format(round($total_amount+$purchase['pur_labor_char']+$purchase['pur_convance_char']-$purchase['pur_discount']));
        $pdf->SetFont('helvetica','B', 12);
        $pdf->Cell(35, 5,  $net_amount, 1, 'R');
        
        $pdf->SetFont('helvetica','BIU', 14);
        $pdf->SetTextColor(23, 54, 93);

        $pdf->SetXY(10, $currentY+20);
        $width = 100;
        $pdf->MultiCell($width, 10, $num_to_words, 0, 'L', 0, 1, '', '', true);
        $pdf->SetFont('helvetica','', 10);
        
        // Close and output PDF
        $pdf->Output('Purchase Invoice_'.$purchase['prefix'].$purchase['pur_id'].'.pdf', 'I');
    }
}
