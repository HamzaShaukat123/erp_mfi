<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use App\Traits\SaveImage;
use App\Models\AC;
use App\Models\Item_entry2;
use App\Models\TBadDabs;
use App\Models\TBadDabs2;
use App\Services\myPDF;


class TBadDabsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use SaveImage;

    public function index()
    {
        $tbad_dabs = TBadDabs::where('tbad_dabs.status', 1)
        ->leftjoin ('tbad_dabs_2', 'tbad_dabs_2.bad_dabs_cod' , '=', 'tbad_dabs.bad_dabs_id')
        ->select(
            'tbad_dabs.bad_dabs_id','tbad_dabs.date','tbad_dabs.reason','tbad_dabs.item_type',
            \DB::raw('SUM(tbad_dabs_2.pc_add) as add_sum'),
            \DB::raw('SUM(tbad_dabs_2.pc_less) as less_sum'),
        )
        ->groupby('tbad_dabs.bad_dabs_id','tbad_dabs.date','tbad_dabs.reason','tbad_dabs.item_type')
        ->get();

        return view('tbad_dabs.index',compact('tbad_dabs'));
    }


    public function create(Request $request)
    {
        $items = Item_entry2::orderBy('item_name', 'asc')->get();
        $coa = AC::orderBy('ac_name', 'asc')->get();

        return view('tbad_dabs.create',compact('items','coa'));
    }

    public function store(Request $request)
    {
        $tbad_dabs = new TBadDabs();
    
        if ($request->has('date') && $request->date) {
            $tbad_dabs->date = $request->date;
        }
        if ($request->has('reason') && $request->reason) {
            $tbad_dabs->reason = $request->reason; 
        }
        
        if ($request->has('item_type') && $request->item_type) {
            $tbad_dabs->item_type = $request->item_type; 
        }
    
        $tbad_dabs->created_by = session('user_id');
        $tbad_dabs->status = 1;
    
        $tbad_dabs->save();
    
        $tbad_id = TBadDabs::latest()->value('bad_dabs_id');
    
        if($request->has('items'))
        {
            for($i=0;$i<$request->items;$i++)
            {
                if(filled($request->item_name[$i]))
                {
                    $tbad_dabs_2 = new TBadDabs2();
                    $tbad_dabs_2->bad_dabs_cod = $tbad_id;
                    $tbad_dabs_2->item_cod = $request->item_code[$i];
                    if ($request->item_remarks[$i]!=null) {
                        $tbad_dabs_2->remarks=$request->item_remarks[$i];
                    }
                    if ($request->qty_add[$i]!=null) {
                        $tbad_dabs_2->pc_add=$request->qty_add[$i];
                    }
                    if ($request->qty_less[$i]!=null) {
                        $tbad_dabs_2->pc_less=$request->qty_less[$i];
                    }
                    $tbad_dabs_2->save();
                }
            }
        }
    
        return redirect()->route('all-tbad-dabs');
    }


    public function destroy(Request $request)
    {
        $tbad_dabs = TBadDabs::where('bad_dabs_id', $request->delete_tbad_dabs_id)->update([
            'status' => '0',
            'updated_by' => session('user_id'),
        ]);

        return redirect()->route('all-tbad-dabs');
    }

    
    public function edit($id)
    {
        $tbad_dabs = TBadDabs::where('bad_dabs_id', $id)->first();
        $tbad_dabs_items = TBadDabs2::where('bad_dabs_cod', $id)->get();
        $tbad_dabs_item_count = count($tbad_dabs_items);
        $items = Item_entry2::orderBy('item_name', 'asc')->get();
    
        // Calculate the total_add and total_less
        $total_add = $tbad_dabs_items->sum('pc_add');
        $total_less = $tbad_dabs_items->sum('pc_less');
    
        return view('tbad_dabs.edit', compact('tbad_dabs', 'tbad_dabs_items', 'items', 'tbad_dabs_item_count', 'total_add', 'total_less'));
    }
    

    public function update(Request $request)
    {
        $tbad_dabs = TBadDabs::where('bad_dabs_id',$request->bad_dabs_id)->get()->first();

        if ($request->has('date') && $request->date) {
            $tbad_dabs->date=$request->date;
        }
        if ($request->has('reason') && $request->reason OR empty($request->reason)) {
            $tbad_dabs->reason=$request->reason;
        }
        if ($request->has('item_type') && $request->item_type) {
            $tbad_dabs->item_type=$request->item_type;
        }

        TBadDabs::where('bad_dabs_id', $request->bad_dabs_id)->update([
            'reason'=>$tbad_dabs->reason,
            'date'=>$tbad_dabs->date,
            'item_type'=>$tbad_dabs->item_type,
            'updated_by' => session('user_id'),
        ]);
        
        TBadDabs2::where('bad_dabs_cod', $request->bad_dabs_id)->delete();
        
        if($request->has('items'))
        {
            for($i=0;$i<$request->items;$i++)
            {

                if(filled($request->item_code[$i]))
                {
                    $tbad_dabs_2 = new TBadDabs2();
                    $tbad_dabs_2->bad_dabs_cod=$request->bad_dabs_id;
                    $tbad_dabs_2->item_cod=$request->item_code[$i];
                    if ($request->remarks[$i]!=null OR empty($request->remarks[$i])) {
                        $tbad_dabs_2->remarks=$request->remarks[$i];
                    }
                    $tbad_dabs_2->pc_add=$request->qty_add[$i];
                    $tbad_dabs_2->pc_less=$request->qty_less[$i];
                    $tbad_dabs_2->save();
                }
            }
        }

        return redirect()->route('all-tbad-dabs');
    }

    public function show(string $id)
    {
        $tbad_dabs = TBadDabs::where('bad_dabs_id', $id)->first();

        $tbad_dabs2 = TBadDabs2::where('bad_dabs_cod',$id)
                ->join('item_entry2 as ie','tbad_dabs_2.item_cod','=','ie.it_cod')
                ->select('tbad_dabs_2.*','ie.item_name')
                ->get();

        return view('tbad_dabs.view',compact('tbad_dabs','tbad_dabs2'));
    }

    public function generatePDF($id)
{
    $tbad_dabs = TBadDabs::where('bad_dabs_id', $id)->first();
    $tbad_dabs2 = TBadDabs2::where('bad_dabs_cod', $id)
        ->join('item_entry2 as ie', 'tbad_dabs_2.item_cod', '=', 'ie.it_cod')
        ->select('tbad_dabs_2.*', 'ie.item_name')
        ->get();

    $pdf = new MyPDF();
    
    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('MFI');
    $pdf->SetTitle('TBad Dabs-'. $tbad_dabs['bad_dabs_id']);
    $pdf->SetSubject('TBad Dabs-'. $tbad_dabs['bad_dabs_id']);
    $pdf->SetKeywords('TBad Dabs, TCPDF, PDF');
    
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

    $heading='<h1 style="font-size:20px;text-align:center;font-style:italic;text-decoration:underline;color:#17365D">Bad Dabs Pipe/Garder</h1>';
    $pdf->writeHTML($heading, true, false, true, false, '');
    $pdf->writeHTML('<style>' . $margin_bottom . '</style>', true, false, true, false, '');

    $html = '<table style="margin-bottom:1rem">';
    $html .= '<tr>';
    $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">ID: <span style="text-decoration: underline;color:#000">' . $tbad_dabs['bad_dabs_id'] . '</span></td>';
    $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Item Type: <span style="text-decoration: underline;color:#000">' . ($tbad_dabs['item_type'] == 1 ? 'Pipes' : ($tbad_dabs['item_type'] == 2 ? 'Garder / TR' : 'Unknown')) . '</span></td>';
    $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Date: <span style="color:#000">' . \Carbon\Carbon::parse($tbad_dabs['date'])->format('d-m-y') . '</span></td>';
    $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Login: <span style="text-decoration: underline;color:#000">' . auth()->user()->name . '</span></td>';
    $html .= '</tr>';
    $html .= '</table>';

    // $pdf->writeHTML($html, true, false, true, false, '');

    $html .= '<table border="0.1px" style="border-collapse: collapse; width: 100%;">';
    $html .= '<tr>';
    $html .= '<td style="font-size:10px;font-weight:bold;font-family:poppins;width: 10%;color:#17365D">Reason</td>';
    $html .= '<td  style="font-size:10px;font-family:poppins;width: 90%;">'.$tbad_dabs['reason'].'</td>';
    $html .= '</tr>';
    $html .= '</table>';

    
    $pdf->writeHTML($html, true, false, true, false, '');

    $html = '<table border="0.3" style="text-align:center;margin-top:10px">';
    $html .= '<tr>';
    $html .= '<th style="width:6%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">S/R</th>';
    $html .= '<th style="width:36%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Item Name</th>';
    $html .= '<th style="width:34%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Description</th>';
    $html .= '<th style="width:12%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Qty Add</th>';
    $html .= '<th style="width:12%;font-size:10px;font-weight:bold;font-family:poppins;color:#17365D">Qty Less</th>';
    $html .= '</tr>';
    $html .= '</table>';

    $pdf->setTableHtml($html);

    $count = 1;
    $total_add = 0;
    $total_less = 0;

    $html .= '<table cellspacing="0" cellpadding="5">';
    foreach ($tbad_dabs2 as $items) {
        // Determine background color based on odd/even rows
        $bg_color = ($count % 2 == 0) ? 'background-color:#f1f1f1' : '';

        $html .= '<tr style="' . $bg_color . '">';
        $html .= '<td style="width:6%;border-right:1px dashed #000;border-left:1px dashed #000; text-align:center">' . $count . '</td>';
        $html .= '<td style="width:36%;border-right:1px dashed #000">' . $items['item_name'] . '</td>';
        $html .= '<td style="width:34%;border-right:1px dashed #000">' . $items['remarks'] . '</td>';
        $html .= '<td style="width:12%;border-right:1px dashed #000; text-align:center">' . $items['pc_add'] . '</td>';
        $total_add += $items['pc_add'];
        $html .= '<td style="width:12%;border-right:1px dashed #000; text-align:center">' . $items['pc_less'] . '</td>';
        $total_less += $items['pc_less'];
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
    $pdf->Cell(40, 5, 'Total Add', 1,1);
    $pdf->Cell(40, 5, 'Total Less', 1,1);


// Column 1 
$pdf->SetTextColor(0, 0, 0);
$pdf->SetXY(50, $currentY);
$pdf->SetFont('helvetica', 'B', 10); // Set font to bold
$pdf->Cell(42, 5, $total_add, 1, 'R');

$pdf->SetXY(50, $currentY + 6.8);
$pdf->Cell(42, 5, $total_less, 1, 'R'); // Use the same font style

       // Close and output PDF
    $pdf->Output('TBad Dabs_'. $tbad_dabs['bad_dabs_id'] . '.pdf', 'I');
}

}