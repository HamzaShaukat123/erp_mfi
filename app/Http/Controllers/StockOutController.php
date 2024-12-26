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
use App\Models\Item_entry;
use App\Models\stock_out;
use App\Models\stock_out_2;
use App\Models\stock_out_att;
use App\Services\myPDF;


class StockOutController extends Controller
{

    use SaveImage;

    public function index()
    {
        $stock_out = stock_out::where('stock_out.status', 1)
        ->leftjoin ('stock_out_2', 'stock_out_2.sales_inv_cod' , '=', 'stock_out.Sal_inv_no')
        ->join('ac','stock_out.account_name','=','ac.ac_code')
        ->select(
            'stock_out.Sal_inv_no','stock_out.sa_date','stock_out.Cash_pur_name','stock_out.Sales_remarks','ac.ac_name',
            'stock_out.pur_inv', 'stock_out.mill_gate', 'stock_out.transporter','stock_out.Cash_pur_address','stock_out.prefix',
            \DB::raw('SUM(stock_out_2.Sales_qty) as qty_sum'),
            \DB::raw('SUM(stock_out_2.weight_pc) as weight_sum'),
        )
        ->groupby('stock_out.Sal_inv_no','stock_out.sa_date','stock_out.Cash_pur_name','stock_out.Sales_remarks','ac.ac_name',
        'stock_out.pur_inv', 'stock_out.mill_gate', 'stock_out.transporter','stock_out.Cash_pur_address','stock_out.prefix' )
        ->get();

        return view('stock_out.index',compact('stock_out'));
    }

    public function addAtt(Request $request)
    {
        $stock_out_id=$request->att_id;

        if($request->hasFile('addAtt')){
            $files = $request->file('addAtt');
            foreach ($files as $file)
            {
                $stock_out_att = new stock_out_att();
                $stock_out_att->created_by = session('user_id');
                $stock_out_att->stock_out_id = $stock_out_id;
                $extension = $file->getClientOriginalExtension();
                $stock_out_att->att_path = $this->StockOutDoc($file,$extension);
                $stock_out_att->save();
            }
        }
        return redirect()->route('all-stock-out');
    }

    public function destroy(Request $request)
    {
        $stock_out = stock_out::where('Sal_inv_no', $request->invoice_id)->update([
            'status' => '0',
            'updated_by' => session('user_id'),
        ]);
        return redirect()->route('all-stock-out');
    }

    public function create(Request $request)
    {
        $items = Item_entry::orderBy('item_name', 'asc')->get();
        $coa = AC::orderBy('ac_name', 'asc')->get();

        return view('stock_out.create',compact('items','coa'));
    }

    public function store(Request $request)
    {
        $stock_out = new stock_out();

        // $stock_out->Sal_inv_no;
        if ($request->has('date') && $request->date) {
            $stock_out->sa_date=$request->date;
        }

        if ($request->has('remarks') && $request->remarks OR empty($request->remarks) ) {
            $stock_out->Sales_remarks=$request->remarks;
        }

        if ($request->has('Cash_pur_name') && $request->Cash_pur_name OR empty($request->Cash_pur_name) ) {
            $stock_out->Cash_pur_name=$request->Cash_pur_name;
        }
        if ($request->has('cash_pur_address') && $request->cash_pur_address OR empty($request->cash_pur_address) ) {
            $stock_out->cash_Pur_address=$request->cash_pur_address;
        }
        if ($request->has('account_name') && $request->account_name) {
            $stock_out->account_name=$request->account_name;
        }

        $stock_out->created_by = session('user_id');
        $stock_out->status=1;

        $stock_out->save();

        $latest_invoice = stock_out::latest()->first();
        $invoice_id = $latest_invoice['Sal_inv_no'];

        if($request->has('items'))
        {
            for($i=0;$i<$request->items;$i++)
            {
                if(filled($request->item_code[$i]))
                {
                    $stock_out_2 = new stock_out_2();
                    $stock_out_2->sales_inv_cod=$invoice_id;
                    $stock_out_2->item_cod=$request->item_code[$i];
                    if ($request->item_remarks[$i]!=null OR empty($request->item_remarks[$i])) {
                        $stock_out_2->remarks=$request->item_remarks[$i];
                    }
                    $stock_out_2->Sales_qty=$request->qty[$i];
                    $stock_out_2->weight_pc=$request->weight[$i];
    
                    $stock_out_2->save();
                }
            }
        }

        if($request->hasFile('att')){
            $files = $request->file('att');
            foreach ($files as $file)
            {
                $stock_out_att = new stock_out_att();
                $stock_out_att->stock_out_id = $invoice_id;
                $extension = $file->getClientOriginalExtension();
                $stock_out_att->att_path = $this->StockOutDoc($file,$extension);
                $stock_out_att->save();
            }
        }

        return redirect()->route('all-stock-out');
    }

    public function show(string $id)
    
    {
        $stock_out = stock_out::where('Sal_inv_no',$id)
                        ->join('ac','stock_out.account_name','=','ac.ac_code')
                        ->first();

        $stock_out_items = stock_out_2::where('sales_inv_cod',$id)
                        ->join('item_entry','stock_out_2.item_cod','=','item_entry.it_cod')
                        ->get();
        return view('stock_out.view',compact('stock_out','stock_out_items'));
    }

    public function edit($id)
    {
        $tstock_in = stock_out::where('Sal_inv_no',$id)->first();
        $tstock_in_items = stock_out_2::where('sales_inv_cod',$id)->get();

        $items = Item_entry::orderBy('item_name', 'asc')->get();
        $coa = AC::orderBy('ac_name', 'asc')->get();

        return view('stock_out.edit', compact('tstock_in','tstock_in_items','items','coa'));
    }

    public function update(Request $request)
    {
        $stock_out = stock_out::where('Sal_inv_no',$request->invoice_no)->get()->first();

        if ($request->has('date') && $request->date) {
            $stock_out->sa_date=$request->date;
        }

        if ($request->has('remarks') && $request->remarks OR empty($request->remarks)) {
            $stock_out->Sales_remarks=$request->remarks;
        }

        if ($request->has('Cash_pur_name') && $request->Cash_pur_name) {
            $stock_out->Cash_pur_name=$request->Cash_pur_name;
        }
        if ($request->has('cash_pur_address') && $request->cash_pur_address) {
            $stock_out->cash_Pur_address=$request->cash_pur_address;
        }
        if ($request->has('account_name') && $request->account_name) {
            $stock_out->account_name=$request->account_name;
        }

        stock_out::where('Sal_inv_no', $request->invoice_no)->update([
            'sa_date'=>$stock_out->sa_date,
            'Sales_remarks'=>$stock_out->Sales_remarks,
            'cash_Pur_address'=>$stock_out->cash_Pur_address,
            'Cash_pur_name'=>$stock_out->Cash_pur_name,
            'account_name'=>$stock_out->account_name,
            'updated_by' => session('user_id'),

        ]);
        
        stock_out_2::where('sales_inv_cod', $request->invoice_no)->delete();
        
        if($request->has('items'))
        {
            for($i=0;$i<$request->items;$i++)
            {
                if(filled($request->item_code[$i]))
                {
                    $stock_out_2 = new stock_out_2();
                    $stock_out_2->sales_inv_cod=$request->invoice_no;
                    $stock_out_2->item_cod=$request->item_code[$i];
                    $stock_out_2->remarks=$request->item_remarks[$i];
                    $stock_out_2->Sales_qty=$request->qty[$i];
                    $stock_out_2->weight_pc=$request->weight[$i];
    
                    $stock_out_2->save();
                }
            }
        }

        if($request->hasFile('att')){
            $files = $request->file('att');
            foreach ($files as $file)
            {
                $stock_out_att = new stock_out_att();
                $stock_out_att->stock_out_id = $request->invoice_no;
                $extension = $file->getClientOriginalExtension();
                $stock_out_att->att_path = $this->StockOutDoc($file,$extension);
                $stock_out_att->save();
            }
        }
        

        return redirect()->route('all-stock-out');
    }

    public function getAttachements(Request $request)
    {
        $stock_out_att = stock_out_att::where('stock_out_id', $request->id)->get();
        
        return $stock_out_att;
    }

    public function deleteAtt($id)
    {
        $doc=stock_out_att::where('att_id', $id)->select('att_path')->first();
        $filePath = public_path($doc['att_path']);
        if (File::exists($filePath)) {
            File::delete($filePath);
            $stock_out_att = stock_out_att::where('att_id', $id)->delete();
            return response()->json(['message' => 'File deleted successfully.']);
        } else {
            return response()->json(['message' => 'File not found.'], 404);
        }	
    }

    public function view($id)
    {
        $doc=stock_out_att::where('att_id', $id)->select('att_path')->first();
        $filePath = public_path($doc['att_path']);
        if (file_exists($filePath)) {
            return Response::file($filePath);
        } 
    }

    public function downloadAtt($id)
    {
        $doc=stock_out_att::where('att_id', $id)->select('att_path')->first();
        $filePath = public_path($doc['att_path']);
        if (file_exists($filePath)) {
            return Response::download($filePath);
        } 
    }

    public function generatePDF($id)
    {
        $stock_out = stock_out::where('Sal_inv_no',$id)
        ->join('ac','stock_out.account_name','=','ac.ac_code')
        ->first();

        $stock_out_items = stock_out_2::where('sales_inv_cod',$id)
                ->join('item_entry','stock_out_2.item_cod','=','item_entry.it_cod')
                ->select('stock_out_2.*','item_entry.item_name')
                ->get();

        $pdf = new MyPDF();

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('MFI');
        $pdf->SetTitle('Stock Out-'.$stock_out['prefix'].$stock_out['Sal_inv_no']);
        $pdf->SetSubject('Stock Out-'.$stock_out['prefix'].$stock_out['Sal_inv_no']);
        $pdf->SetKeywords('Stock Out, TCPDF, PDF');
                   
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

        $heading='<h1 style="font-size:20px;text-align:center;font-style:italic;text-decoration:underline;color:#17365D">Stock Out Doors</h1>';
        $pdf->writeHTML($heading, true, false, true, false, '');
        $pdf->writeHTML('<style>' . $margin_bottom . '</style>', true, false, true, false, '');

        $html = '<table style="margin-bottom:1rem">';
        $html .= '<tr>';
        $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">ID: &nbsp;<span style="text-decoration: underline;color:#000">'.$stock_out['prefix'].$stock_out['Sal_inv_no'].'</span></td>';
        $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Date: &nbsp;<span style="color:#000">'.\Carbon\Carbon::parse($stock_out['sa_date'])->format('d-m-y').'</span></td>';
        // $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Login: &nbsp; <span style="text-decoration: underline;color:#000">Hamza</span></td>';
        $html .= '</tr>';
        $html .= '</table>';

        // $pdf->writeHTML($html, true, false, true, false, '');
        
        $html .= '<table border="0.1px" style="border-collapse: collapse; width: 100%;">';
        $html .= '<tr>';
        $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;width: 15%;color:#17365D">Karigar Name</td>';
        $html .= '<td  style="font-size:10px;font-family:poppins;width: 85%;">'.$stock_out['ac_name'].'</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;width: 15%;color:#17365D">Remarks</td>';
        $html .= '<td  style="font-size:10px;font-family:poppins;width: 85%;">'.$stock_out['Sales_remarks'].'</td>';
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
        foreach ($stock_out_items as $items) {
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
        $pdf->Output('Stock Out_'.$stock_out['prefix'].$stock_out['Sal_inv_no'].'.pdf', 'I');
    }
}