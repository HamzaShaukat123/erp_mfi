<?php
namespace App\Http\Controllers;
use TCPDF;

use Illuminate\Http\Request;
use App\Models\Item_entry2;
use App\Models\Item_Groups;
use App\Models\AC;
use App\Models\tquotation;
use App\Models\tquotation_2;
use App\Models\tquotation_att;
use App\Models\gd_pipe_item_stock9_much;
use Illuminate\Support\Facades\File;
use App\Traits\SaveImage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use App\Services\myPDF;


class TquotationController extends Controller
{
    use SaveImage;

    public function index()
    {
        $quot2 = tquotation::where('tquotation.status', 1)
            ->leftjoin('tquotation_2', 'tquotation_2.sales_inv_cod', '=', 'tquotation.Sale_inv_no')
            ->join('ac as acc_name', 'acc_name.ac_code', '=', 'tquotation.account_name')
            ->join('ac as disp_to', 'disp_to.ac_code', '=', 'tquotation.Cash_pur_name_ac')
            ->select(
                'tquotation.Sale_inv_no', 'tquotation.sa_date', 'acc_name.ac_name as acc_name', 'tquotation.pur_ord_no',
                'disp_to.ac_name as disp_to', 'tquotation.Cash_pur_name', 'tquotation.Sales_Remarks', 'tquotation.sales_against', 'tquotation.prefix', 'tquotation.tc',
                'tquotation.ConvanceCharges', 'tquotation.LaborCharges', 'tquotation.Bill_discount',
                \DB::raw('SUM(tquotation_2.weight_pc * tquotation_2.Sales_qty2) as weight_sum'),
                \DB::raw('SUM(((tquotation_2.Sales_qty2 * tquotation_2.sales_price) + ((tquotation_2.Sales_qty2 * tquotation_2.sales_price) * (tquotation_2.discount / 100))) * tquotation_2.length) as total_bill')
            )
            ->groupby(
                'tquotation.Sale_inv_no', 'tquotation.sa_date', 'acc_name.ac_name', 'tquotation.pur_ord_no',
                'disp_to.ac_name', 'tquotation.Cash_pur_name', 'tquotation.Sales_Remarks', 'tquotation.sales_against', 'tquotation.prefix', 'tquotation.tc',
                'tquotation.ConvanceCharges', 'tquotation.LaborCharges', 'tquotation.Bill_discount'
            )
            ->get();
    
        return view('tquotation.index', compact('quot2'));
    }
    
    public function create(Request $request)
    {
        $items = Item_entry2::all();
        $coa = AC::all();
        return view('tquotation.create',compact('items','coa'));
    }

    public function store(Request $request)
    {
        
        $quot2 = new tquotation();

        if ($request->has('sa_date') && $request->sa_date) {
            $quot2->sa_date=$request->sa_date;
        }
        if ($request->has('pur_ord_no') && $request->pur_ord_no) {
            $quot2->pur_ord_no=$request->pur_ord_no;
        }
        if ($request->has('isInduced') && $request->isInduced==1) {
            $quot2->sale_against=$request->sale_against;
        }
        if ($request->has('sales_against') && $request->sales_against) {
            $quot2->sales_against=$request->sales_against;
        }
        if ($request->has('account_name') && $request->account_name) {
            $quot2->account_name=$request->account_name;
        }
        if ($request->has('disp_account_name') && $request->disp_account_name) {
            $quot2->Cash_pur_name_ac=$request->disp_account_name;
        }
        if ($request->has('Cash_pur_name') && $request->Cash_pur_name) {
            $quot2->Cash_pur_name=$request->Cash_pur_name;
        }
        if ($request->has('cash_Pur_address') && $request->cash_Pur_address) {
            $quot2->cash_Pur_address=$request->cash_Pur_address;
        }
        if ($request->has('Sales_Remarks') && $request->Sales_Remarks) {
            $quot2->Sales_Remarks=$request->Sales_Remarks;
        }
        if ($request->has('tc') && $request->tc) {
            $quot2->tc=$request->tc;
        }
        if ($request->has('ConvanceCharges') && $request->ConvanceCharges) {
            $quot2->ConvanceCharges=$request->ConvanceCharges;
        }
        if ($request->has('LaborCharges') && $request->LaborCharges) {
            $quot2->LaborCharges=$request->LaborCharges;
        }
        if ($request->has('Bill_discount') && $request->Bill_discount) {
            $quot2->Bill_discount=$request->Bill_discount;
        }

        $quot2->created_by = session('user_id');

        $quot2->save();

        $pur_2_id = tquotation::latest()->first();

        if($request->has('items'))
         {
            for($i=0;$i<$request->items;$i++)
            {
                if(filled($request->item_name[$i]))
                {
                    $tquotation_2 = new tquotation_2();

                    $tquotation_2->sales_inv_cod=$pur_2_id['Sale_inv_no'];
                    $tquotation_2->item_cod=$request->item_cod[$i];

                    if ($request->remarks[$i]!=null) {
                        $tquotation_2->remarks=$request->remarks[$i];
                    }
                    if ($request->pur2_qty2[$i]!=null) {
                        $tquotation_2->Sales_qty2=$request->pur2_qty2[$i];
                    }
                    if ($request->pur2_per_unit[$i]!=null) {
                        $tquotation_2->sales_price=$request->pur2_per_unit[$i];
                    }
                    if ($request->weight_per_piece[$i]!=null) {
                        $tquotation_2->weight_pc=$request->weight_per_piece[$i];
                    }
                    if ($request->pur2_len[$i]!=null) {
                        $tquotation_2->length=$request->pur2_len[$i];
                    }
                    if ($request->pur2_price_date[$i]!=null) {
                        $tquotation_2->rat_dat=$request->pur2_price_date[$i];
                    }
                    if ($request->pur2_percentage[$i]!=null) {
                        $tquotation_2->discount=$request->pur2_percentage[$i];
                    }
                    
                    $tquotation_2->save();
                }
            }
         }     

         if($request->hasFile('att')){
            $files = $request->file('att');
            foreach ($files as $file)
            {
                $pur2Att = new tquotation_att();
                $pur2Att->pur2_id = $pur_2_id['Sale_inv_no'];
                $extension = $file->getClientOriginalExtension();
                $pur2Att->att_path = $this->tquotDoc($file,$extension);
                $pur2Att->save();
            }
        }

        return redirect()->route('all-tquotation');
    }

    public function edit($id)
    {
        $items = Item_entry2::orderBy('item_name', 'asc')->get();
        $coa = AC::orderBy('ac_name', 'asc')->get();

        $pur2 = tquotation::where('tquotation.Sale_inv_no',$id)->first();
        $pur2_item = tquotation_2::where('tquotation_2.sales_inv_cod',$id)->get();

        return view('tquotation.edit',compact('pur2','pur2_item','items','coa'));
    }

    public function update(Request $request)
    {

        $pur2 = tquotation::where('Sale_inv_no',$request->pur2_id)->get()->first();

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
        if ($request->has('tc') && $request->tc OR empty($request->tc)) {
            $pur2->tc=$request->tc;
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

        tquotation::where('Sale_inv_no', $request->pur2_id)->update([
            'sa_date'=>$pur2->sa_date,
            'pur_ord_no'=>$pur2->pur_ord_no,
            'sales_against'=>$pur2->sales_against,
            'account_name'=>$pur2->account_name,
            'Cash_pur_name_ac'=>$pur2->Cash_pur_name_ac,
            'Cash_pur_name'=>$pur2->Cash_pur_name,
            'cash_Pur_address'=>$pur2->cash_Pur_address,
            'tc'=>$pur2->tc,
            'Sales_Remarks'=>$pur2->Sales_Remarks,
            'ConvanceCharges'=>$pur2->ConvanceCharges,
            'LaborCharges'=>$pur2->LaborCharges,
            'Bill_discount'=>$pur2->Bill_discount,
            'updated_by' => session('user_id'),
        ]);

        tquotation_2::where('sales_inv_cod', $request->pur2_id)->delete();

        if($request->has('items'))
        {
            for($i=0;$i<$request->items;$i++)
            {
                if(filled($request->item_name[$i]))
                {
                    $tquotation_2 = new tquotation_2();

                    $tquotation_2->sales_inv_cod=$request->pur2_id;
                    $tquotation_2->item_cod=$request->item_cod[$i];

                    if ($request->remarks[$i]!=null OR empty($request->remarks[$i])) {
                        $tquotation_2->remarks=$request->remarks[$i];
                    }
                    if ($request->pur2_qty2[$i]!=null) {
                        $tquotation_2->Sales_qty2=$request->pur2_qty2[$i];
                    }
                    if ($request->pur2_per_unit[$i]!=null) {
                        $tquotation_2->sales_price=$request->pur2_per_unit[$i];
                    }
                    if ($request->weight_per_piece[$i]!=null) {
                        $tquotation_2->weight_pc=$request->weight_per_piece[$i];
                    }
                    if ($request->pur2_len[$i]!=null) {
                        $tquotation_2->length=$request->pur2_len[$i];
                    }
                    if ($request->pur2_price_date[$i]!=null OR empty($request->pur2_price_date[$i])) {
                        $tquotation_2->rat_dat=$request->pur2_price_date[$i];
                    }
                    if ($request->pur2_percentage[$i]!=null) {
                        $tquotation_2->discount=$request->pur2_percentage[$i];
                    }
                    
                    $tquotation_2->save();
                }
            }
        }

       

        if($request->hasFile('att')){
            $files = $request->file('att');
            foreach ($files as $file)
            {
                $pur2Att = new tquotation_att();
                $pur2Att->pur2_id = $request->pur2_id;
                $extension = $file->getClientOriginalExtension();
                $pur2Att->att_path = $this->tquotDoc($file,$extension);
                $pur2Att->save();
            }
        }

        return redirect()->route('all-tquotation');
    }

    public function destroy(Request $request)
    {
        tquotation::where('Sale_inv_no', $request->delete_quot2)->update([
            'status' => '0',
            'updated_by' => session('user_id'),
        ]);
        return redirect()->route('all-tquotation');
    }

    public function show(string $id)
    {
        $pur = tquotation::where('Sale_inv_no',$id)
                ->join('ac as acc_name','tquotation.account_name','=','acc_name.ac_code')
                ->join('ac as dispt_to','tquotation.Cash_pur_name_ac','=','dispt_to.ac_code')
                ->select('tquotation.*','dispt_to.ac_name as disp_to','acc_name.ac_name as ac_name', 
                'acc_name.address as address', 'acc_name.phone_no as phone_no')
                ->first();

        $pur2 = tquotation_2::where('sales_inv_cod',$id)
                ->join('item_entry2 as ie','tquotation_2.item_cod','=','ie.it_cod')
                ->select('tquotation_2.*','ie.item_name')
                ->get();

        return view('tquotation.view',compact('pur','pur2'));
    }

    public function getAttachements(Request $request)
    {
        $pur2_atts = tquotation_att::where('pur2_id', $request->id)->get();
        
        return $pur2_atts;
    }

    public function getItems($id){

        $quot1= tquotation::where('Sale_inv_no',$id)->get()->first();

        $quot2 = tquotation_2::where('sales_inv_cod',$id)
        ->join('item_entry as ie','tquotation_2.item_cod','=','ie.it_cod')
        ->select('tquotation_2.*','ie.item_name')
        ->get();

        return response()->json([
            'pur1' => $quot1,
            'pur2' => $quot2,
        ]);
    }

    public function deleteAtt($id)
    {
        $doc=tquotation_att::where('att_id', $id)->select('att_path')->first();
        $filePath = public_path($doc['att_path']);
        if (File::exists($filePath)) {
            File::delete($filePath);
            $pur2_att = tquotation_att::where('att_id', $id)->delete();
            return response()->json(['message' => 'File deleted successfully.']);
        } else {
            return response()->json(['message' => 'File not found.'], 404);
        }	
    }

    public function view($id)
    {
        $doc=tquotation_att::where('att_id', $id)->select('att_path')->first();
        $filePath = public_path($doc['att_path']);
        if (file_exists($filePath)) {
            return Response::file($filePath);
        } 
    }

    public function downloadAtt($id)
    {
        $doc=tquotation_att::where('att_id', $id)->select('att_path')->first();
        $filePath = public_path($doc['att_path']);
        if (file_exists($filePath)) {
            return Response::download($filePath);
        } 
    }
    
    public function getavailablestock($id) {
        $result = gd_pipe_item_stock9_much::where('it_cod', $id)->select('opp_bal')->get();
        return $result;
    }
    
    public function showAllPDF($id)

    {
        $purchase = tquotation::where('Sale_inv_no',$id)
                ->join('ac as acc_name','tquotation.account_name','=','acc_name.ac_code')
                ->join('ac as dispt_to','tquotation.Cash_pur_name_ac','=','dispt_to.ac_code')
                ->select('tquotation.*','dispt_to.ac_name as disp_to','acc_name.ac_name as ac_name', 
                'acc_name.address as address', 'acc_name.phone_no as phone_no')
                ->first();

                $purchase_items = tquotation_2::where('sales_inv_cod',$id)
                ->join('item_entry2 as ie','tquotation_2.item_cod','=','ie.it_cod')
                ->select('tquotation_2.*','ie.item_name')
                ->get();
                
                
        $pdf = new MyPDF();

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('MFI');
        $pdf->SetTitle('Quotation-'.$purchase['prefix'].$purchase['Sale_inv_no']);
        $pdf->SetSubject('Quotation-'.$purchase['prefix'].$purchase['Sale_inv_no']);
        $pdf->SetKeywords('Quotation, TCPDF, PDF');
                   
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

        $heading='<h1 style="font-size:20px;text-align:center;font-style:italic;text-decoration:underline;color:#17365D">Quotation</h1>';
        $pdf->writeHTML($heading, true, false, true, false, '');
        $pdf->writeHTML('<style>' . $margin_bottom . '</style>', true, false, true, false, '');

        $html = '<table style="margin-bottom:1rem">';
        $html .= '<tr>';
        $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Quotation#: &nbsp;<span style="text-decoration: underline;color:#000">'.$purchase['prefix'].$purchase['Sale_inv_no'].'</span></td>';
        $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Date: &nbsp;<span style="color:#000">'.\Carbon\Carbon::parse($purchase['sa_date'])->format('d-m-y').'</span></td>';
        $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">PO No: <span style="text-decoration: underline;color:#000">'.$purchase['pur_ord_no'].'</span></td>';
        //$html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Login: &nbsp; <span style="text-decoration: underline;color:#000">Hamza</span></td>';
        $html .= '</tr>';
        $html .= '</table>';

        // $pdf->writeHTML($html, true, false, true, false, '');

        $html .= '<table border="0.1px" style="border-collapse: collapse;">';
        $html .= '<tr>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Account Name </td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['ac_name'].'</td>';
        $html .= '<td width="20%" width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Dispatch From</td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['disp_to'].'</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td width="20%" width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D" >Address </td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['address'].'</td>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Address</td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['cash_Pur_address'].'</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Phone </td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['phone_no'].'</td>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Name OF Person</td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['Cash_pur_name'].'</td>';
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
            $html .= '<td style="width:20%;border-right:1px dashed #000">' . $items['remarks'] . '</td>';
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
        
        
         // terms and condition starts here
         $currentY = $pdf->GetY();

         $pdf->SetFont('helvetica','BIU', 14);
         $pdf->SetTextColor(23, 54, 93);
 
         $pdf->SetXY(10, $currentY+10);
         $pdf->Cell(35, 5,  'Terms & Conditions:' , 0, 'L');
 
         $pdf->SetFont('helvetica','', 11);
         $pdf->SetTextColor(255, 0, 0);
 
         $width = 185;
         $pdf->MultiCell($width, 10, $purchase['tc'], 0, 'L', 0, 1, '', '', true);
 
         // terms and condition ends here
 


        // Close and output PDF
        $pdf->Output('Quotation_'.$purchase['prefix'].$purchase['Sale_inv_no'].'.pdf', 'I');
    }
    

    public function noLengthPDF($id)
    {
        $purchase = tquotation::where('Sale_inv_no',$id)
        ->join('ac as acc_name','tquotation.account_name','=','acc_name.ac_code')
        ->join('ac as dispt_to','tquotation.Cash_pur_name_ac','=','dispt_to.ac_code')
        ->select('tquotation.*','dispt_to.ac_name as disp_to','acc_name.ac_name as ac_name', 
        'acc_name.address as address', 'acc_name.phone_no as phone_no')
        ->first();

        $purchase_items = tquotation_2::where('sales_inv_cod',$id)
        ->join('item_entry2 as ie','tquotation_2.item_cod','=','ie.it_cod')
        ->select('tquotation_2.*','ie.item_name')
        ->get();
        
        
$pdf = new MyPDF();

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('MFI');
$pdf->SetTitle('Quotation-'.$purchase['prefix'].$purchase['Sale_inv_no']);
$pdf->SetSubject('Quotation-'.$purchase['prefix'].$purchase['Sale_inv_no']);
$pdf->SetKeywords('Quotation, TCPDF, PDF');
           
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

$heading='<h1 style="font-size:20px;text-align:center;font-style:italic;text-decoration:underline;color:#17365D">Quotation</h1>';
$pdf->writeHTML($heading, true, false, true, false, '');
$pdf->writeHTML('<style>' . $margin_bottom . '</style>', true, false, true, false, '');

$html = '<table style="margin-bottom:1rem">';
$html .= '<tr>';
$html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Quotation#: &nbsp;<span style="text-decoration: underline;color:#000">'.$purchase['prefix'].$purchase['Sale_inv_no'].'</span></td>';
$html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Date: &nbsp;<span style="color:#000">'.\Carbon\Carbon::parse($purchase['sa_date'])->format('d-m-y').'</span></td>';
$html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">PO No: <span style="text-decoration: underline;color:#000">'.$purchase['pur_ord_no'].'</span></td>';
//$html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Login: &nbsp; <span style="text-decoration: underline;color:#000">Hamza</span></td>';
$html .= '</tr>';
$html .= '</table>';

// $pdf->writeHTML($html, true, false, true, false, '');

$html .= '<table border="0.1px" style="border-collapse: collapse;">';
$html .= '<tr>';
$html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Account Name </td>';
$html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['ac_name'].'</td>';
$html .= '<td width="20%" width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Dispatch From</td>';
$html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['disp_to'].'</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<td width="20%" width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D" >Address </td>';
$html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['address'].'</td>';
$html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Address</td>';
$html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['cash_Pur_address'].'</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Phone </td>';
$html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['phone_no'].'</td>';
$html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Name OF Person</td>';
$html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['Cash_pur_name'].'</td>';
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
            $html .= '<td style="width:21%;border-right:1px dashed #000">' . $items['remarks'] . '</td>';
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

        
         // terms and condition starts here
         $currentY = $pdf->GetY();

         $pdf->SetFont('helvetica','BIU', 14);
         $pdf->SetTextColor(23, 54, 93);
 
         $pdf->SetXY(10, $currentY+10);
         $pdf->Cell(35, 5,  'Terms & Conditions:' , 0, 'L');
 
         $pdf->SetFont('helvetica','', 11);
         $pdf->SetTextColor(255, 0, 0);
 
         $width = 185;
         $pdf->MultiCell($width, 10, $purchase['tc'], 0, 'L', 0, 1, '', '', true);
 
         // terms and condition ends here
 
        
        // Close and output PDF
        $pdf->Output('Quotation_'.$purchase['prefix'].$purchase['Sale_inv_no'].'.pdf', 'I');
    }

    public function onlyPriceQtyPDF($id)
    {
        $purchase = tquotation::where('Sale_inv_no',$id)
        ->join('ac as acc_name','tquotation.account_name','=','acc_name.ac_code')
        ->join('ac as dispt_to','tquotation.Cash_pur_name_ac','=','dispt_to.ac_code')
        ->select('tquotation.*','dispt_to.ac_name as disp_to','acc_name.ac_name as ac_name', 
        'acc_name.address as address', 'acc_name.phone_no as phone_no')
        ->first();

        $purchase_items = tquotation_2::where('sales_inv_cod',$id)
        ->join('item_entry2 as ie','tquotation_2.item_cod','=','ie.it_cod')
        ->select('tquotation_2.*','ie.item_name')
        ->get();
        
        
        $pdf = new MyPDF();

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('MFI');
        $pdf->SetTitle('Quotation-'.$purchase['prefix'].$purchase['Sale_inv_no']);
        $pdf->SetSubject('Quotation-'.$purchase['prefix'].$purchase['Sale_inv_no']);
        $pdf->SetKeywords('Quotation, TCPDF, PDF');
                
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

        $heading='<h1 style="font-size:20px;text-align:center;font-style:italic;text-decoration:underline;color:#17365D">Quotation</h1>';
        $pdf->writeHTML($heading, true, false, true, false, '');
        $pdf->writeHTML('<style>' . $margin_bottom . '</style>', true, false, true, false, '');

        $html = '<table style="margin-bottom:1rem">';
        $html .= '<tr>';
        $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Quotation#: &nbsp;<span style="text-decoration: underline;color:#000">'.$purchase['prefix'].$purchase['Sale_inv_no'].'</span></td>';
        $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Date: &nbsp;<span style="color:#000">'.\Carbon\Carbon::parse($purchase['sa_date'])->format('d-m-y').'</span></td>';
        $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">PO No: <span style="text-decoration: underline;color:#000">'.$purchase['pur_ord_no'].'</span></td>';
        //$html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Login: &nbsp; <span style="text-decoration: underline;color:#000">Hamza</span></td>';
        $html .= '</tr>';
        $html .= '</table>';

        // $pdf->writeHTML($html, true, false, true, false, '');

        $html .= '<table border="0.1px" style="border-collapse: collapse;">';
        $html .= '<tr>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Account Name </td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['ac_name'].'</td>';
        $html .= '<td width="20%" width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Dispatch From</td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['disp_to'].'</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td width="20%" width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D" >Address </td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['address'].'</td>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Address</td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['cash_Pur_address'].'</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Phone </td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['phone_no'].'</td>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Name OF Person</td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['Cash_pur_name'].'</td>';
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
            $html .= '<td style="width:25%;border-right:1px dashed #000">' . $items['remarks'] . '</td>';
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

        
         // terms and condition starts here
         $currentY = $pdf->GetY();

         $pdf->SetFont('helvetica','BIU', 14);
         $pdf->SetTextColor(23, 54, 93);
 
         $pdf->SetXY(10, $currentY+10);
         $pdf->Cell(35, 5,  'Terms & Conditions:' , 0, 'L');
 
         $pdf->SetFont('helvetica','', 11);
         $pdf->SetTextColor(255, 0, 0);
 
         $width = 185;
         $pdf->MultiCell($width, 10, $purchase['tc'], 0, 'L', 0, 1, '', '', true);
 
         // terms and condition ends here
 
        
        // Close and output PDF
        $pdf->Output('Quotation_'.$purchase['prefix'].$purchase['Sale_inv_no'].'.pdf', 'I');
    }


    public function onlyWeightQtyPDF($id)
    {
        $purchase = tquotation::where('Sale_inv_no', $id)
            ->join('ac as acc_name', 'tquotation.account_name', '=', 'acc_name.ac_code')
            ->join('ac as dispt_to', 'tquotation.Cash_pur_name_ac', '=', 'dispt_to.ac_code')
            ->select(
                'tquotation.*',
                'dispt_to.ac_name as disp_to',
                'acc_name.ac_name as ac_name',
                'acc_name.address as address',
                'acc_name.phone_no as phone_no'
            )
            ->first();
    
        $purchase_items = tquotation_2::where('sales_inv_cod', $id)
            ->join('item_entry2 as ie', 'tquotation_2.item_cod', '=', 'ie.it_cod')
            ->select('tquotation_2.*', 'ie.item_name')
            ->get();
    
            $pdf = new MyPDF();

            // Set document information
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('MFI');
            $pdf->SetTitle('Quotation-'.$purchase['prefix'].$purchase['Sale_inv_no']);
            $pdf->SetSubject('Quotation-'.$purchase['prefix'].$purchase['Sale_inv_no']);
            $pdf->SetKeywords('Quotation, TCPDF, PDF');
                    
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
    
            $heading='<h1 style="font-size:20px;text-align:center;font-style:italic;text-decoration:underline;color:#17365D">Quotation</h1>';
            $pdf->writeHTML($heading, true, false, true, false, '');
            $pdf->writeHTML('<style>' . $margin_bottom . '</style>', true, false, true, false, '');
    
            $html = '<table style="margin-bottom:1rem">';
            $html .= '<tr>';
            $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Quotation#: &nbsp;<span style="text-decoration: underline;color:#000">'.$purchase['prefix'].$purchase['Sale_inv_no'].'</span></td>';
            $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Date: &nbsp;<span style="color:#000">'.\Carbon\Carbon::parse($purchase['sa_date'])->format('d-m-y').'</span></td>';
            $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">PO No: <span style="text-decoration: underline;color:#000">'.$purchase['pur_ord_no'].'</span></td>';
            //$html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Login: &nbsp; <span style="text-decoration: underline;color:#000">Hamza</span></td>';
            $html .= '</tr>';
            $html .= '</table>';
    
            // $pdf->writeHTML($html, true, false, true, false, '');
    
            $html .= '<table border="0.1px" style="border-collapse: collapse;">';
            $html .= '<tr>';
            $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Account Name </td>';
            $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['ac_name'].'</td>';
            $html .= '<td width="20%" width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Dispatch From</td>';
            $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['disp_to'].'</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td width="20%" width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D" >Address </td>';
            $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['address'].'</td>';
            $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Address</td>';
            $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['cash_Pur_address'].'</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Phone </td>';
            $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['phone_no'].'</td>';
            $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Name OF Person</td>';
            $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['Cash_pur_name'].'</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Remarks </td>';
            $html .= '<td width="80%" style="font-size:10px;font-family:poppins;">'.$purchase['Sales_Remarks'].'</td>';
            $html .= '</tr>';
            $html .= '</table>';
    
            
            $pdf->writeHTML($html, true, false, true, false, '');
        
            $html = '<table border="0.3" style="text-align:center;margin-top:10px">';
            $html .= '<tr>';
            $html .= '<th style="width:8%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">S/R</th>';
            $html .= '<th style="width:35%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Item Name</th>';
            $html .= '<th style="width:28%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Description</th>';
            $html .= '<th style="width:13%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Qty</th>';;
            $html .= '<th style="width:16%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Weight</th>';
            $html .= '</tr>';
            $html .= '</table>';
    
            $pdf->setTableHtml($html);
    
        // Items table header
        $html = '<table border="0.5" cellpadding="5" style="width:100%; border-collapse:collapse; text-align:center;">';
        $html .= '<tr style="background-color:#f2f2f2; font-weight:bold; color:#17365D;">';
        $html .= '<th style="width:8%;">S/R</th>';
        $html .= '<th style="width:35%;">Item Name</th>';
        $html .= '<th style="width:28%;">Description</th>';
        $html .= '<th style="width:13%;">Qty</th>';
        $html .= '<th style="width:16%;">Weight</th>';
        $html .= '</tr>';
    
        // Items data
        $count = 1;
        $total_weight = 0;
        $total_quantity = 0;
    
        foreach ($purchase_items as $items) {
            $bg_color = ($count % 2 == 0) ? 'background-color:#f9f9f9;' : '';
            $weight = $items['Sales_qty2'] * $items['weight_pc'];
            $html .= '<tr style="' . $bg_color . '">';
            $html .= '<td>' . $count . '</td>';
            $html .= '<td>' . $items['item_name'] . '</td>';
            $html .= '<td>' . $items['remarks'] . '</td>';
            $html .= '<td>' . $items['Sales_qty2'] . '</td>';
            $html .= '<td>' . round($weight, 2) . '</td>';
            $html .= '</tr>';
    
            $total_quantity += $items['Sales_qty2'];
            $total_weight += $weight;
            $count++;
        }
    
        // Totals row
        $html .= '<tr style="background-color:#e6f7ff; font-weight:bold;">';
        $html .= '<td colspan="3" style="text-align:right;">Total:</td>';
        $html .= '<td>' . number_format($total_quantity, 0) . '</td>';
        $html .= '<td>' . number_format($total_weight, 2) . '</td>';
        $html .= '</tr>';
    
        $html .= '</table>';
        $pdf->writeHTML($html, true, false, true, false, '');
    
        // Close and output PDF
        $pdf->Output('Quotation_' . $purchase['prefix'] . $purchase['Sale_inv_no'] . '.pdf', 'I');
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
        elseif($request->print_type==4){
            $this->onlyWeightQtyPDF($request->print_sale2);
        }
    }
}
