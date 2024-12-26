<?php
namespace App\Http\Controllers;
use TCPDF;

use Illuminate\Http\Request;
use App\Models\Item_entry2;
use App\Models\Item_Groups;
use App\Models\AC;
use App\Models\weight;
use App\Models\weight_2;
use App\Models\weight_att;
use App\Models\gd_pipe_item_stock9_much;
use Illuminate\Support\Facades\File;
use App\Traits\SaveImage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use App\Services\myPDF;


class WeightController extends Controller
{
    use SaveImage;

    public function index()
    {
        $quot2 = weight::where('weight.status', 1)
            ->leftjoin('weight_2', 'weight_2.sales_inv_cod', '=', 'weight.Sale_inv_no')
            ->join('ac as acc_name', 'acc_name.ac_code', '=', 'weight.account_name')
            ->join('ac as disp_to', 'disp_to.ac_code', '=', 'weight.Cash_pur_name_ac')
            ->select(
                'weight.Sale_inv_no', 'weight.sa_date', 'acc_name.ac_name as acc_name', 'weight.pur_ord_no',
                'disp_to.ac_name as disp_to', 'weight.Cash_pur_name', 'weight.Sales_Remarks', 'weight.sales_against', 'weight.prefix',
                \DB::raw('SUM(weight_2.weight_pc * weight_2.Sales_qty2) as weight_sum'),
                
            )
            ->groupby(
                'weight.Sale_inv_no', 'weight.sa_date', 'acc_name.ac_name', 'weight.pur_ord_no',
                'disp_to.ac_name', 'weight.Cash_pur_name', 'weight.Sales_Remarks', 'weight.sales_against', 'weight.prefix'
            )
            ->get();
    
        return view('weight.index', compact('quot2'));
    }
    
    public function create(Request $request)
    {
        $items = Item_entry2::all();
        $coa = AC::all();
        return view('weight.create',compact('items','coa'));
    }

    public function store(Request $request)
    {
        
        $quot2 = new weight();

        if ($request->has('sa_date') && $request->sa_date) {
            $quot2->sa_date=$request->sa_date;
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

        $quot2->created_by = session('user_id');

        $quot2->save();

        $pur_2_id = weight::latest()->first();

        if($request->has('items'))
         {
            for($i=0;$i<$request->items;$i++)
            {
                if(filled($request->item_name[$i]))
                {
                    $weight_2 = new weight_2();

                    $weight_2->sales_inv_cod=$pur_2_id['Sale_inv_no'];
                    $weight_2->item_cod=$request->item_cod[$i];

                    if ($request->remarks[$i]!=null) {
                        $weight_2->remarks=$request->remarks[$i];
                    }
                    if ($request->pur2_qty2[$i]!=null) {
                        $weight_2->Sales_qty2=$request->pur2_qty2[$i];
                    }
                    if ($request->pur2_per_unit[$i]!=null) {
                        $weight_2->sales_price=$request->pur2_per_unit[$i];
                    }
                    if ($request->weight_per_piece[$i]!=null) {
                        $weight_2->weight_pc=$request->weight_per_piece[$i];
                    }
                    if ($request->pur2_len[$i]!=null) {
                        $weight_2->length=$request->pur2_len[$i];
                    }
                    if ($request->pur2_price_date[$i]!=null) {
                        $weight_2->rat_dat=$request->pur2_price_date[$i];
                    }
                    if ($request->pur2_percentage[$i]!=null) {
                        $weight_2->discount=$request->pur2_percentage[$i];
                    }
                    
                    $weight_2->save();
                }
            }
         }     

         if($request->hasFile('att')){
            $files = $request->file('att');
            foreach ($files as $file)
            {
                $pur2Att = new weight_att();
                $pur2Att->pur2_id = $pur_2_id['Sale_inv_no'];
                $extension = $file->getClientOriginalExtension();
                $pur2Att->att_path = $this->weightDoc($file,$extension);
                $pur2Att->save();
            }
        }

        return redirect()->route('all-weight');
    }

    public function edit($id)
    {
        $items = Item_entry2::orderBy('item_name', 'asc')->get();
        $coa = AC::orderBy('ac_name', 'asc')->get();

        $pur2 = weight::where('weight.Sale_inv_no',$id)->first();
        $pur2_item = weight_2::where('weight_2.sales_inv_cod',$id)->get();

        return view('weight.edit',compact('pur2','pur2_item','items','coa'));
    }

    public function update(Request $request)
    {

        $pur2 = weight::where('Sale_inv_no',$request->pur2_id)->get()->first();

        if ($request->has('sa_date') && $request->sa_date) {
            $pur2->sa_date=$request->sa_date;
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

        weight::where('Sale_inv_no', $request->pur2_id)->update([
            'sa_date'=>$pur2->sa_date,
            'account_name'=>$pur2->account_name,
            'Cash_pur_name_ac'=>$pur2->Cash_pur_name_ac,
            'Cash_pur_name'=>$pur2->Cash_pur_name,
            'cash_Pur_address'=>$pur2->cash_Pur_address,
            'Sales_Remarks'=>$pur2->Sales_Remarks,
            'updated_by' => session('user_id'),
        ]);

        weight_2::where('sales_inv_cod', $request->pur2_id)->delete();

        if($request->has('items'))
        {
            for($i=0;$i<$request->items;$i++)
            {
                if(filled($request->item_name[$i]))
                {
                    $weight_2 = new weight_2();

                    $weight_2->sales_inv_cod=$request->pur2_id;
                    $weight_2->item_cod=$request->item_cod[$i];

                    if ($request->remarks[$i]!=null OR empty($request->remarks[$i])) {
                        $weight_2->remarks=$request->remarks[$i];
                    }
                    if ($request->pur2_qty2[$i]!=null) {
                        $weight_2->Sales_qty2=$request->pur2_qty2[$i];
                    }
                    if ($request->pur2_per_unit[$i]!=null) {
                        $weight_2->sales_price=$request->pur2_per_unit[$i];
                    }
                    if ($request->weight_per_piece[$i]!=null) {
                        $weight_2->weight_pc=$request->weight_per_piece[$i];
                    }
                    if ($request->pur2_len[$i]!=null) {
                        $weight_2->length=$request->pur2_len[$i];
                    }
                    if ($request->pur2_price_date[$i]!=null OR empty($request->pur2_price_date[$i])) {
                        $weight_2->rat_dat=$request->pur2_price_date[$i];
                    }
                    if ($request->pur2_percentage[$i]!=null) {
                        $weight_2->discount=$request->pur2_percentage[$i];
                    }
                    
                    $weight_2->save();
                }
            }
        }

       

        if($request->hasFile('att')){
            $files = $request->file('att');
            foreach ($files as $file)
            {
                $pur2Att = new weight_att();
                $pur2Att->pur2_id = $request->pur2_id;
                $extension = $file->getClientOriginalExtension();
                $pur2Att->att_path = $this->weightDoc($file,$extension);
                $pur2Att->save();
            }
        }

        return redirect()->route('all-weight');
    }

    public function destroy(Request $request)
    {
        weight::where('Sale_inv_no', $request->delete_quot2)->update([
            'status' => '0',
            'updated_by' => session('user_id'),
        ]);
        return redirect()->route('all-weight');
    }

    public function show(string $id)
    {
        $pur = weight::where('Sale_inv_no',$id)
                ->join('ac as acc_name','weight.account_name','=','acc_name.ac_code')
                ->join('ac as dispt_to','weight.Cash_pur_name_ac','=','dispt_to.ac_code')
                ->select('weight.*','dispt_to.ac_name as disp_to','acc_name.ac_name as ac_name', 
                'acc_name.address as address', 'acc_name.phone_no as phone_no')
                ->first();

        $pur2 = weight_2::where('sales_inv_cod',$id)
                ->join('item_entry2 as ie','weight_2.item_cod','=','ie.it_cod')
                ->select('weight_2.*','ie.item_name')
                ->get();

        return view('weight.view',compact('pur','pur2'));
    }

    public function getAttachements(Request $request)
    {
        $pur2_atts = weight_att::where('pur2_id', $request->id)->get();
        
        return $pur2_atts;
    }

    public function getItems($id){

        $quot1= weight::where('Sale_inv_no',$id)->get()->first();

        $quot2 = weight_2::where('sales_inv_cod',$id)
        ->join('item_entry as ie','weight_2.item_cod','=','ie.it_cod')
        ->select('weight_2.*','ie.item_name')
        ->get();

        return response()->json([
            'pur1' => $quot1,
            'pur2' => $quot2,
        ]);
    }

    public function deleteAtt($id)
    {
        $doc=weight_att::where('att_id', $id)->select('att_path')->first();
        $filePath = public_path($doc['att_path']);
        if (File::exists($filePath)) {
            File::delete($filePath);
            $pur2_att = weight_att::where('att_id', $id)->delete();
            return response()->json(['message' => 'File deleted successfully.']);
        } else {
            return response()->json(['message' => 'File not found.'], 404);
        }	
    }

    public function view($id)
    {
        $doc=weight_att::where('att_id', $id)->select('att_path')->first();
        $filePath = public_path($doc['att_path']);
        if (file_exists($filePath)) {
            return Response::file($filePath);
        } 
    }

    public function downloadAtt($id)
    {
        $doc=weight_att::where('att_id', $id)->select('att_path')->first();
        $filePath = public_path($doc['att_path']);
        if (file_exists($filePath)) {
            return Response::download($filePath);
        } 
    }
    
    public function getavailablestock($id) {
        $result = gd_pipe_item_stock9_much::where('it_cod', $id)->select('opp_bal')->get();
        return $result;
    }
    
   
    public function generatePDF($id)
    {
        $purchase = weight::where('Sale_inv_no',$id)
        ->join('ac as acc_name','weight.account_name','=','acc_name.ac_code')
        ->join('ac as dispt_to','weight.Cash_pur_name_ac','=','dispt_to.ac_code')
        ->select('weight.*','dispt_to.ac_name as disp_to','acc_name.ac_name as ac_name', 
        'acc_name.address as address', 'acc_name.phone_no as phone_no')
        ->first();

        $purchase_items = weight_2::where('sales_inv_cod',$id)
        ->join('item_entry2 as ie','weight_2.item_cod','=','ie.it_cod')
        ->select('weight_2.*','ie.item_name')
        ->get();
        
        
        $pdf = new MyPDF();

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('MFI');
        $pdf->SetTitle('Weight-'.$purchase['prefix'].$purchase['Sale_inv_no']);
        $pdf->SetSubject('Weight-'.$purchase['prefix'].$purchase['Sale_inv_no']);
        $pdf->SetKeywords('Weight, TCPDF, PDF');
                
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

        $heading='<h1 style="font-size:20px;text-align:center;font-style:italic;text-decoration:underline;color:#17365D">Weight</h1>';
        $pdf->writeHTML($heading, true, false, true, false, '');
        $pdf->writeHTML('<style>' . $margin_bottom . '</style>', true, false, true, false, '');

        $html = '<table style="margin-bottom:1rem">';
        $html .= '<tr>';
        $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Weight#: &nbsp;<span style="text-decoration: underline;color:#000">'.$purchase['prefix'].$purchase['Sale_inv_no'].'</span></td>';
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
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Name Of Person</td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['Cash_pur_names'].'</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Phone </td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['phone_no'].'</td>';
        $html .= '<td width="20%" style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Address</td>';
        $html .= '<td width="30%" style="font-size:10px;font-family:poppins;">'.$purchase['cash_Pur_address'].'</td>';
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
                $html .= '<th style="width:25%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Item Name</th>';
                $html .= '<th style="width:23%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Description</th>';
                $html .= '<th style="width:10%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Qty</th>';
                $html .= '<th style="width:10%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Price</th>';
                $html .= '<th style="width:14%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Amount</th>';
                $html .= '<th style="width:12%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Weight</th>';
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
                    $html .= '<td style="width:25%;border-right:1px dashed #000">' . $items['item_name'] . '</td>';
                    $html .= '<td style="width:23%;border-right:1px dashed #000">' . $items['remarks'] . '</td>';
                    $html .= '<td style="width:10%;border-right:1px dashed #000; text-align:center">' . $items['Sales_qty2'] . '</td>';
                    $total_quantity += $items['Sales_qty2'];
                    // Calculate the list price
                    $price = (($items['sales_price']) + (( $items['sales_price']) * ($items['discount'] / 100))) * $items['length'];
                    $html .= '<td style="width:10%;border-right:1px dashed #000; text-align:center">' . $price . '</td>';

                    // Calculate the total weight and amount
                    $amount = (($items['Sales_qty2'] * $items['sales_price']) + (($items['Sales_qty2'] * $items['sales_price']) * ($items['discount'] / 100))) * $items['length'];
                    $html .= '<td style="width:14%;border-right:1px dashed #000; text-align:center">' . round($amount, 2) . '</td>';
                    $total_amount += $amount;

                    $weight = $items['Sales_qty2'] * $items['weight_pc'];
                    $html .= '<td style="width:12%;border-right:1px dashed #000; text-align:center">' . round($weight, 2) . '</td>';
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

                
            

                // Column 3
                $pdf->SetFont('helvetica','B', 10);
                $pdf->SetTextColor(23, 54, 93);

                $pdf->SetXY(120, $currentY);
                $pdf->Cell(45, 5, 'Total Amount', 1,1);
              
                
                // // Column 4
                $pdf->SetFont('helvetica','', 10);
                $pdf->SetTextColor(0, 0, 0);

                $pdf->SetXY(165, $currentY);
                $pdf->Cell(35, 5, $total_amount, 1, 'R');
             
                
                // Close and output PDF
                $pdf->Output('Weight_'.$purchase['prefix'].$purchase['Sale_inv_no'].'.pdf', 'I');
            }

  
}
