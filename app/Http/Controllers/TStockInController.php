<?php

namespace App\Http\Controllers;
use TCPDF;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use App\Traits\SaveImage;
use App\Models\AC;
use App\Models\Item_entry2;
use App\Models\tstock_in;
use App\Models\tstock_in_2;
use App\Models\tstock_in_att;
use App\Models\tpurchase;
use App\Services\myPDF;


class TStockInController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use SaveImage;

    public function index()
    {
        
        $tstock_in = tstock_in::where('tstock_in.status', 1)
        ->leftjoin ('tstock_in_2', 'tstock_in_2.sales_inv_cod' , '=', 'tstock_in.Sal_inv_no')
        ->join('ac','tstock_in.account_name','=','ac.ac_code')
        ->select(
            'tstock_in.Sal_inv_no','tstock_in.sa_date','tstock_in.Cash_pur_name','tstock_in.Sales_remarks','ac.ac_name',
            'tstock_in.pur_inv', 'tstock_in.mill_gate', 'tstock_in.transporter','tstock_in.Cash_pur_address','tstock_in.prefix','tstock_in.item_type',
            \DB::raw('SUM(tstock_in_2.Sales_qty) as qty_sum'),
            \DB::raw('SUM(tstock_in_2.Sales_qty*tstock_in_2.weight_pc) as weight_sum'),
        )
        ->groupby('tstock_in.Sal_inv_no','tstock_in.sa_date','tstock_in.Cash_pur_name','tstock_in.Sales_remarks','ac.ac_name',
        'tstock_in.pur_inv', 'tstock_in.mill_gate', 'tstock_in.transporter','tstock_in.Cash_pur_address','tstock_in.prefix','tstock_in.item_type' )
        ->get();

        return view('tstock_in.index',compact('tstock_in'));
    }

    public function addAtt(Request $request)
    {
        $tstock_in_id=$request->att_id;

        if($request->hasFile('addAtt')){
            $files = $request->file('addAtt');
            foreach ($files as $file)
            {
                $tstock_in_att = new tstock_in_att();
                $tstock_in_att->created_by = session('user_id');
                $tstock_in_att->tstock_in_id = $tstock_in_id;
                $extension = $file->getClientOriginalExtension();
                $tstock_in_att->att_path = $this->tStockInDoc($file,$extension);
                $tstock_in_att->save();
            }
        }
        return redirect()->route('all-tstock-in');
    }

    public function destroy(Request $request)
    {
        $tstock_in = tstock_in::where('Sal_inv_no', $request->invoice_id)->update([
            'status' => '0',
            'updated_by' => session('user_id'),
        ]);
        return redirect()->route('all-tstock-in');
    }

    public function create(Request $request)
    {
        $items = Item_entry2::orderBy('item_name', 'asc')->get();
        $coa = AC::orderBy('ac_name', 'asc')->get();

        return view('tstock_in.create',compact('items','coa'));
    }

    public function store(Request $request)
    {
        $tstock_in = new tstock_in();

        if ($request->has('date') && $request->date) {
            $tstock_in->sa_date=$request->date;
        }
        if ($request->has('pur_inv') && $request->pur_inv OR empty($request->pur_inv) ) {
            $tstock_in->pur_inv=$request->pur_inv;
        }
        if ($request->has('remarks') && $request->remarks OR empty($request->remarks) ) {
            $tstock_in->Sales_remarks=$request->remarks;
        }
        if ($request->has('mill_gate') && $request->mill_gate OR empty($request->mill_gate) ) {
            $tstock_in->mill_gate=$request->mill_gate;
        }
        if ($request->has('Cash_pur_name') && $request->Cash_pur_name OR empty($request->Cash_pur_name) ) {
            $tstock_in->Cash_pur_name=$request->Cash_pur_name;
        }
        if ($request->has('cash_pur_address') && $request->cash_pur_address OR empty($request->cash_pur_address) ) {
            $tstock_in->cash_Pur_address=$request->cash_pur_address;
        }
        if ($request->has('transporter') && $request->transporter OR empty($request->transporter) ) {
            $tstock_in->transporter=$request->transporter;
        }
        
        if ($request->has('item_type') && $request->item_type OR empty($request->item_type) ) {
            $tstock_in->item_type=$request->item_type;
        }
        if ($request->has('account_name') && $request->account_name) {
            $tstock_in->account_name=$request->account_name;
        }
        $tstock_in->created_by = session('user_id');
        $tstock_in->status=1;

        $tstock_in->save();

        $latest_invoice = tstock_in::latest()->first();
        $invoice_id = $latest_invoice['Sal_inv_no'];

        if($request->has('items'))
        {
            for($i=0;$i<$request->items;$i++)
            {
                if(filled($request->item_code[$i]))
                {
                    $tstock_in_2 = new tstock_in_2();
                    $tstock_in_2->sales_inv_cod=$invoice_id;
                    $tstock_in_2->item_cod=$request->item_code[$i];
                    if ($request->item_remarks[$i]!=null OR empty($request->item_remarks[$i])) {
                        $tstock_in_2->remarks=$request->item_remarks[$i];
                    }
                    $tstock_in_2->Sales_qty=$request->qty[$i];
                    $tstock_in_2->weight_pc=$request->weight[$i];
    
                    $tstock_in_2->save();
                }
            }
        }

        if($request->hasFile('att')){
            $files = $request->file('att');
            foreach ($files as $file)
            {
                $tstock_in_att = new tstock_in_att();
                $tstock_in_att->tstock_in_id = $invoice_id;
                $extension = $file->getClientOriginalExtension();
                $tstock_in_att->att_path = $this->tStockInDoc($file,$extension);
                $tstock_in_att->save();
            }
        }

        if($request->has('isInduced') && $request->isInduced == 1){

            $tpurchase = new tpurchase();
            $SalinducedID=$latest_invoice['Sal_inv_no'];
            $prefix=$latest_invoice['prefix'];
            $pur_inv = $prefix.''.$SalinducedID;
            $tpurchase->sales_against = $pur_inv;
            tpurchase::where('Sale_inv_no', $request->sale_against)->update([
                'sales_against'=>$tpurchase->sales_against,
            ]);
        } 

        return redirect()->route('all-tstock-in');
    }

    public function show(string $id)
    {
        $tstock_in = tstock_in::where('Sal_inv_no',$id)
                        ->join('ac','tstock_in.account_name','=','ac.ac_code')
                        ->first();

        $tstock_in_items = tstock_in_2::where('sales_inv_cod',$id)
                        ->join('item_entry2','tstock_in_2.item_cod','=','item_entry2.it_cod')
                        ->get();
        return view('tstock_in.view',compact('tstock_in','tstock_in_items'));
    }

    public function edit($id)
    {
        $tstock_in = tstock_in::where('Sal_inv_no',$id)->first();
        $tstock_in_items = tstock_in_2::where('sales_inv_cod',$id)->get();

        $items = Item_entry2::orderBy('item_name', 'asc')->get();
        $coa = AC::orderBy('ac_name', 'asc')->get();

        return view('tstock_in.edit', compact('tstock_in','tstock_in_items','items','coa'));
    }

    public function update(Request $request)
    {
        $tstock_in = tstock_in::where('Sal_inv_no',$request->invoice_no)->get()->first();

        if ($request->has('date') && $request->date) {
            $tstock_in->sa_date=$request->date;
        }
        if ($request->has('pur_inv') && $request->pur_inv OR empty($request->pur_inv)) {
            $tstock_in->pur_inv=$request->pur_inv;
        }
        if ($request->has('remarks') && $request->remarks OR empty($request->remarks)) {
            $tstock_in->Sales_remarks=$request->remarks;
        }
        if ($request->has('mill_gate') && $request->mill_gate OR empty($request->mill_gate)) {
            $tstock_in->mill_gate=$request->mill_gate;
        }
        if ($request->has('Cash_pur_name') && $request->Cash_pur_name OR empty($request->Cash_pur_name)) {
            $tstock_in->Cash_pur_name=$request->Cash_pur_name;
        }
        if ($request->has('cash_pur_address') && $request->cash_pur_address OR empty($request->cash_pur_address)) {
            $tstock_in->cash_Pur_address=$request->cash_pur_address;
        }
        if ($request->has('transporter') && $request->transporter OR empty($request->transporter)) {
            $tstock_in->transporter=$request->transporter;
        }
        if ($request->has('item_type') && $request->item_type) {
            $tstock_in->item_type=$request->item_type;
        }
        
        if ($request->has('account_name') && $request->account_name) {
            $tstock_in->account_name=$request->account_name;
        }

        tstock_in::where('Sal_inv_no', $request->invoice_no)->update([
            'sa_date'=>$tstock_in->sa_date,
            'pur_inv'=>$tstock_in->pur_inv,
            'mill_gate'=>$tstock_in->mill_gate,
            'Sales_remarks'=>$tstock_in->Sales_remarks,
            'cash_Pur_address'=>$tstock_in->cash_Pur_address,
            'Cash_pur_name'=>$tstock_in->Cash_pur_name,
            'transporter'=>$tstock_in->transporter,
            'account_name'=>$tstock_in->account_name,
            'item_type'=>$tstock_in->item_type,
            'updated_by' => session('user_id'),
        ]);
        
        tstock_in_2::where('sales_inv_cod', $request->invoice_no)->delete();
        
        if($request->has('items'))
        {
            for($i=0;$i<$request->items;$i++)
            {
                if(filled($request->item_code[$i]))
                {
                    $tstock_in_2 = new tstock_in_2();
                    $tstock_in_2->sales_inv_cod=$request->invoice_no;
                    $tstock_in_2->item_cod=$request->item_code[$i];
                    $tstock_in_2->remarks=$request->item_remarks[$i];
                    $tstock_in_2->Sales_qty=$request->qty[$i];
                    $tstock_in_2->weight_pc=$request->weight[$i];
    
                    $tstock_in_2->save();
                }
            }
        }

        if($request->hasFile('att')){
            $files = $request->file('att');
            foreach ($files as $file)
            {
                $tstock_in_att = new tstock_in_att();
                $tstock_in_att->tstock_in_id = $request->invoice_no;
                $extension = $file->getClientOriginalExtension();
                $tstock_in_att->att_path = $this->tStockInDoc($file,$extension);
                $tstock_in_att->save();
            }
        }
        

        return redirect()->route('all-tstock-in');
    }

    public function getAttachements(Request $request)
    {
        $tstock_in_att = tstock_in_att::where('tstock_in_id', $request->id)->get();
        return $tstock_in_att;
    }

    public function deleteAtt($id)
    {
        $doc=tstock_in_att::where('att_id', $id)->select('att_path')->first();
        $filePath = public_path($doc['att_path']);
        if (File::exists($filePath)) {
            File::delete($filePath);
            $tstock_in_att = tstock_in_att::where('att_id', $id)->delete();
            return response()->json(['message' => 'File deleted successfully.']);
        } else {
            return response()->json(['message' => 'File not found.'], 404);
        }	
    }

    public function view($id)
    {
        $doc=tstock_in_att::where('att_id', $id)->select('att_path')->first();
        $filePath = public_path($doc['att_path']);
        if (file_exists($filePath)) {
            return Response::file($filePath);
        } 
    }

    public function downloadAtt($id)
    {
        $doc=tstock_in_att::where('att_id', $id)->select('att_path')->first();
        $filePath = public_path($doc['att_path']);
        if (file_exists($filePath)) {
            return Response::download($filePath);
        } 
    }


    public function generatePDF($id)
    {
        $tstock_in = tstock_in::where('Sal_inv_no',$id)
        ->join('ac','tstock_in.account_name','=','ac.ac_code')
        ->first();

        $tstock_in_items = tstock_in_2::where('sales_inv_cod',$id)
                ->join('item_entry2','tstock_in_2.item_cod','=','item_entry2.it_cod')
                ->select('tstock_in_2.*','item_entry2.item_name')
                ->get();

        $pdf = new MyPDF();

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('MFI');
        $pdf->SetTitle('TStock In-'.$tstock_in['prefix'].$tstock_in['Sal_inv_no']);
        $pdf->SetSubject('TStock In-'.$tstock_in['prefix'].$tstock_in['Sal_inv_no']);
        $pdf->SetKeywords('TStock In, TCPDF, PDF');
                   
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

        $heading='<h1 style="font-size:20px;text-align:center;font-style:italic;text-decoration:underline;color:#17365D">Stock In Pipe/Garder</h1>';
        $pdf->writeHTML($heading, true, false, true, false, '');
        $pdf->writeHTML('<style>' . $margin_bottom . '</style>', true, false, true, false, '');

        $html = '<table style="margin-bottom:1rem">';
        $html .= '<tr>';
        $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">ID: &nbsp;<span style="text-decoration: underline;color:#000">'.$tstock_in['prefix'].$tstock_in['Sal_inv_no'].'</span></td>';
        $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Date: &nbsp;<span style="color:#000">'.\Carbon\Carbon::parse($tstock_in['sa_date'])->format('d-m-y').'</span></td>';
        $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Mill Inv No: <span style="text-decoration: underline;color:#000">'.$tstock_in['mill_gate'].'</span></td>';
        //$html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Login: &nbsp; <span style="text-decoration: underline;color:#000">Hamza</span></td>';
        $html .= '</tr>';
        $html .= '</table>';

        // $pdf->writeHTML($html, true, false, true, false, '');

        $html .= '<table border="0.1px" style="border-collapse: collapse;">';
        $html .= '<tr>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Comapny Name </td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$tstock_in['ac_name'].'</td>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Item Type</td>';

        if ($tstock_in['item_type'] == 1) {
            $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">Pipes</td>';
        } elseif ($tstock_in['item_type'] == 2) {
            $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">Garder / TR</td>';
        } else {
            $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">Unknown</td>';
        }
        
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td width="20%" width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D" >Address </td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$tstock_in['address'].'</td>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Transporter</td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$tstock_in['transporter'].'</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Phone </td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$tstock_in['phone_no'].'</td>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Purchase Invoice</td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$tstock_in['pur_inv'].'</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Remarks </td>';
        $html .= '<td width="80%" style="font-size:10px;font-family:poppins;">'.$tstock_in['Sales_remarks'].'</td>';
        $html .= '</tr>';
        $html .= '</table>';
        
        $pdf->writeHTML($html, true, false, true, false, '');
    
        $html = '<table border="0.3" style="text-align:center;margin-top:10px">';
        $html .= '<tr>';
        $html .= '<th style="width:6%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">S/R</th>';
        $html .= '<th style="width:36%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Item Name</th>';
        $html .= '<th style="width:33%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Description</th>';
        $html .= '<th style="width:12%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Qty</th>';
        $html .= '<th style="width:14%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Weight</th>';
        $html .= '</tr>';
        $html .= '</table>';

        $pdf->setTableHtml($html);

        $count = 1;
        $total_weight = 0;
        $total_quantity = 0;
        $total_amount = 0;

        $html .= '<table cellspacing="0" cellpadding="5">';
        foreach ($tstock_in_items as $items) {
            // Determine background color based on odd/even rows
            $bg_color = ($count % 2 == 0) ? 'background-color:#f1f1f1' : '';

            $html .= '<tr style="' . $bg_color . '">';
            $html .= '<td style="width:6%;border-right:1px dashed #000;border-left:1px dashed #000; text-align:center">' . $count . '</td>';
            $html .= '<td style="width:36%;border-right:1px dashed #000">' . $items['item_name'] . '</td>';
            $html .= '<td style="width:33%;border-right:1px dashed #000">' . $items['remarks'] . '</td>';
            $html .= '<td style="width:12%;border-right:1px dashed #000; text-align:center">' . $items['Sales_qty'] . '</td>';
            $total_quantity += $items['Sales_qty'];

            // Calculate the total weight and amount
            $weight= $items['Sales_qty'] * $items['weight_pc'];
            $html .= '<td style="width:14%;border-right:1px dashed #000; text-align:center">' . $weight . '</td>';
            $total_weight += $weight;

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

        
        // Close and output PDF
        $pdf->Output('TStock In_'.$tstock_in['prefix'].$tstock_in['Sal_inv_no'].'.pdf', 'I');
    }
}

