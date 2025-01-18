<?php

namespace App\Http\Controllers;

use App\Services\myPDF;
use App\Models\AC;
use NumberFormatter;
use App\Models\pdc_att;
use App\Models\pdc;
use App\Traits\SaveImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class PDCController extends Controller
{
    use SaveImage;

    public function index()
    {
        $jv1 = pdc::where('pdc.status', 1)
                ->leftjoin('ac as d_ac', 'd_ac.ac_code', '=', 'pdc.ac_dr_sid')
                ->join('ac as c_ac', 'c_ac.ac_code', '=', 'pdc.ac_cr_sid')
                ->select('pdc.*', 
                'd_ac.ac_name as debit_account', 
                'c_ac.ac_name as credit_account')
                ->get();
        $acc = AC::where('status', 1)->orderBy('ac_name', 'asc')->get();

        return view('pdc.index',compact('jv1','acc'));
    }

    public function show(string $id)
    {
        // Retrieve the record with joined debit and credit account details
        $jv1 = pdc::where('pdc.pdc_id', $id)
                ->join('ac as d_ac', 'd_ac.ac_code', '=', 'pdc.ac_dr_sid')
                ->join('ac as c_ac', 'c_ac.ac_code', '=', 'pdc.ac_cr_sid')
                ->select('pdc.*', 
                'd_ac.ac_name as debit_account', 
                'c_ac.ac_name as credit_account')
                ->first();
    
        // Point to the correct Blade view file: show.blade.php
        return view('pdc_id.show', compact('jv1'));
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ac_dr_sid' => 'required',
            'ac_cr_sid' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $jv1 = new pdc();
        $jv1->created_by = session('user_id');

        if ($request->has('ac_dr_sid') && $request->ac_dr_sid) {
            $jv1->ac_dr_sid=$request->ac_dr_sid;
        }
        if ($request->has('ac_cr_sid') && $request->ac_cr_sid) {
            $jv1->ac_cr_sid=$request->ac_cr_sid;
        }
        if ($request->has('amount') && $request->amount OR $request->amount==0 ) {
            $jv1->amount=$request->amount;
        }
        if ($request->has('date') && $request->date) {
            $jv1->date=$request->date;
        }
        if ($request->has('chqdate') && $request->chqdate) {
            $jv1->chqdate=$request->chqdate;
        }
        if ($request->has('remarks') && $request->remarks  OR empty($request->remarks)) {
            $jv1->remarks=$request->remarks;
        }
        if ($request->has('bankname') && $request->bankname  OR empty($request->bankname)) {
            $jv1->bankname=$request->bankname;
        }
        if ($request->has('instrumentnumber') && $request->instrumentnumber  OR empty($request->instrumentnumber)) {
            $jv1->instrumentnumber=$request->instrumentnumber;
        }
        $jv1->save();

        $latest_jv1 = pdc::latest()->first();

        if($request->hasFile('att')){
            $files = $request->file('att');
            foreach ($files as $file)
            {
                $jv1_att = new pdc_att();
                $jv1_att->pdc_id = $latest_jv1['pdc_id'];
                $extension = $file->getClientOriginalExtension();
                $jv1_att->att_path = $this->pdcDoc($file,$extension);
                $jv1_att->save();
            }
        }
        return redirect()->route('all-pdc');
    }
    
    public function update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'update_ac_dr_sid' => 'required',
            'update_ac_cr_sid' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $jv1 = pdc::where('pdc_id', $request->update_pdc_id)->get()->first();

        if ($request->has('update_ac_dr_sid') && $request->update_ac_dr_sid) {
            $jv1->ac_dr_sid=$request->update_ac_dr_sid;
        }
        if ($request->has('update_ac_cr_sid') && $request->update_ac_cr_sid) {
            $jv1->ac_cr_sid=$request->update_ac_cr_sid;
        }
        if ($request->has('update_amount') && $request->update_amount OR $request->update_amount==0 ) {
            $jv1->amount=$request->update_amount;
        }
        if ($request->has('update_date') && $request->update_date) {
            $jv1->date=$request->update_date;
        }
        if ($request->has('update_chqdate') && $request->update_chqdate OR empty($request->update_chqdate))  {
            $jv1->chqdate=$request->update_chqdate;
        }
        if ($request->has('update_bankname') && $request->update_bankname OR empty($request->update_bankname))  {
            $jv1->bankname=$request->update_bankname;
        }
        if ($request->has('update_instrumentnumber') && $request->update_instrumentnumber OR empty($request->update_instrumentnumber))  {
            $jv1->instrumentnumber=$request->update_instrumentnumber;
        }
        if ($request->has('update_remarks') && $request->update_remarks OR empty($request->update_remarks))  {
            $jv1->remarks=$request->update_remarks;
        }
    
        pdc::where('pdc_id', $request->update_pdc_id)->update([
            'ac_dr_sid'=>$jv1->ac_dr_sid,
            'ac_cr_sid'=>$jv1->ac_cr_sid,
            'amount'=>$jv1->amount,
            'date'=>$jv1->date,
            'chqdate'=>$jv1->chqdate,
            'bankname'=>$jv1->bankname,
            'instrumentnumber'=>$jv1->instrumentnumber,
            'remarks'=>$jv1->remarks,
            'updated_by' => session('user_id'),
        ]);

        if($request->hasFile('update_att')){
            
            // jv1_att::where('jv1_id', $request->update_pdc_id)->delete();
            $files = $request->file('update_att');
            foreach ($files as $file)
            {
                $jv1_att = new pdc_att();
                $jv1_att->pdc_id =  $request->update_pdc_id;
                $extension = $file->getClientOriginalExtension();
                $jv1_att->att_path = $this->pdcDoc($file,$extension);
                $jv1_att->save();
            }
        }

        return redirect()->route('all-pdc');
    }

    public function addAtt(Request $request)
    {
        $jv1_id=$request->att_id;

        if($request->hasFile('addAtt')){
            $files = $request->file('addAtt');
            foreach ($files as $file)
            {
                $jv1_att = new pdc_att();
                $jv1_att->created_by = session('user_id');                
                $jv1_att->pdc_id = $pdc_id;
                $extension = $file->getClientOriginalExtension();
                $jv1_att->att_path = $this->pdcDoc($file,$extension);
                $jv1_att->save();
            }
        }
        return redirect()->route('all-pdc');

    }

    public function destroy(Request $request)
    {
        $jv1 = pdc::where('pdc_id', $request->delete_pdc_id)->update([
            'status' => '0',
            'updated_by' => session('user_id'),
        ]);
        return redirect()->route('all-pdc');
    }

    public function getAttachements(Request $request)
    {
        $jv1_atts = pdc_att::where('pdc_id', $request->id)->get();
        return $jv1_atts;
    }

    public function getPDCDetails(Request $request)
    {
        $jv1_details = pdc::where('pdc_id', $request->id)->get()->first();
        return $jv1_details;
    }

    public function view($id)
    {
        $doc=pdc_att::where('att_id', $id)->select('att_path')->first();
        $filePath = public_path($doc['att_path']);
        if (file_exists($filePath)) {
            return Response::file($filePath);
        } 
    }

    public function downloadAtt($id)
    {
        $doc=jpdc_att::where('att_id', $id)->select('att_path')->first();
        $filePath = public_path($doc['att_path']);
        if (file_exists($filePath)) {
            return Response::download($filePath);
        } 
    }

    public function deleteAtt($id)
    {
        $doc=pdc_att::where('att_id', $id)->select('att_path')->first();
        $filePath = public_path($doc['att_path']);

        if (File::exists($filePath)) {
            File::delete($filePath);
            $jv1_att = jv1_att::where('att_id', $id)->delete();
            return response()->json(['message' => 'File deleted successfully.']);
        } else {
            return response()->json(['message' => 'File not found.'], 404);
        }
    }

    public function print($id)
    {

        $jv1 = pdc::where('pdc.pdc_id', $id)
        ->join('ac as d_ac', 'd_ac.ac_code', '=', 'pdc.ac_dr_sid')
        ->join('ac as c_ac', 'c_ac.ac_code', '=', 'pdc.ac_cr_sid')
        ->select('pdc.*', 
        'd_ac.ac_name as debit_account', 
        'c_ac.ac_name as credit_account')
        ->first();

        $pdf = new MyPDF();

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('MFI');
        $pdf->SetTitle('JV1 # '.$jv1['pdc_id']);
        $pdf->SetSubject('JV1 # '.$jv1['pdc_id']);
        $pdf->SetKeywords('Journal Voucher, TCPDF, PDF');
        $pdf->setPageOrientation('L');
               
        // Add a page
        $pdf->AddPage();
           
        $pdf->setCellPadding(1.2); // Set padding for all cells in the table

        $margin_top = '.margin-top {
            margin-top: 10px;
        }';
        // $pdf->writeHTML('<style>' . $margin_top . '</style>', true, false, true, false, '');

        // margin bottom
        $margin_bottom = '.margin-bottom {
            margin-bottom: 4px;
        }';
        // $pdf->writeHTML('<style>' . $margin_bottom . '</style>', true, false, true, false, '');

        $heading = '<h1 style="font-size:20px;text-align:center;font-style:italic;text-decoration:underline;color:#17365D">Journal Voucher 1</h1>';

        $pdf->writeHTML($heading, true, false, true, false, '');
        $pdf->writeHTML('<style>' . $margin_bottom . '</style>', true, false, true, false, '');

        $html = '<table style="margin-bottom:1rem">';
        $html .= '<tr>';
        $html .= '<td style="font-size:12px;font-weight:bold;color:#17365D;font-family:poppins"> Voucher No: <span style="text-decoration: underline;color:black;">'.$jv1['pdc_id'].'</span></td>';
        $html .= '<td style="font-size:12px;font-weight:bold;color:#17365D;font-family:poppins;text-align:right"> Date: <span style="color:black;font-weight:normal;">' . \Carbon\Carbon::parse($jv1['date'])->format('d-m-y') . '</span></td>';
        $html .= '</tr>';
        $html .= '</table>';

        $html .= '<table style="margin-bottom:1rem">';
       
        $html .= '<tr>';
        $html .= '<td width="10%" style="font-size:12px;font-weight:bold;color:#17365D;font-family:poppins">Remarks:</td>';
        $html .= '<td width="78%" style="color:black;font-weight:normal;">'.$jv1['remarks'].'</td>';
        $html .= '</tr>';
        $html .= '</table>';

        // $pdf->writeHTML($html, true, false, true, false, '');

        $pdf->writeHTML($html, true, false, true, false, '');

        $html = '<table border="1" style="border-collapse: collapse;" >';
        $html .= '<tr>';
        $html .= '<th style="width:40%;color:#17365D;font-weight:bold;">Account Debit</th>';
        $html .= '<th style="width:40%;color:#17365D;font-weight:bold;">Account Credit</th>';
        $html .= '<th style="width:20%;color:#17365D;font-weight:bold;">Amount</th>';
        $html .= '</tr>';
        $html .= '</table>';
        
        // $pdf->writeHTML($html, true, false, true, false, '');

        $count=1;
        $total_credit=0;
        $total_debit=0;

        $html .= '<table cellspacing="0" cellpadding="5">';
        $html .= '<tr>';
        $html .= '<td style="width:40%;">'.$jv1['debit_account'].'</td>';
        $html .= '<td style="width:40%;">'.$jv1['credit_account'].'</td>';
        $html .= '<td style="width:20%;">' . number_format($jv1['amount'], 0) . '</td>';

        $html .= '</tr>';
        
        $html .= '</table>';
        $pdf->writeHTML($html, true, false, true, false, '');

        $pdf->writeHTML('<style>' . $margin_bottom . '</style>', true, false, true, false, '');
        $pdf->writeHTML('<style>' . $margin_bottom . '</style>', true, false, true, false, '');
        $pdf->writeHTML('<style>' . $margin_bottom . '</style>', true, false, true, false, '');

        // Column 3
        $roundedTotal= round($jv1['amount']);
        $num_to_words=$pdf->convertCurrencyToWords($roundedTotal);

        // $number = floor($jv1['amount']); // Remove decimals (round down)
        // $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
        // $numberText=$f->format($number);
        // $formattedWords = ucwords(strtolower($numberText));

        $words='<h1 style="text-decoration:underline;font-style:italic;color:#17365D">'.$num_to_words.'</h1>';
        $pdf->writeHTML($words, true, false, true, false, '');


        $currentY = $pdf->GetY();

        $style = array(
            'T' => array('width' => 0.75),  // Only top border with width 0.75
        );

        // Set text color
        $pdf->SetTextColor(23, 54, 93); // RGB values for #17365D
        // First Cell
        $pdf->SetXY(50, $currentY+50);
        $pdf->Cell(50, 0, "Accountant's Signature", $style, 1, 'C');

        // Second Cell
        $pdf->SetXY(200, $currentY+50);
        $pdf->Cell(50, 0, "Customer's Signature", $style, 1, 'C');

        $pdf->Output('jv1_'.$jv1['pdc_id'].'.pdf', 'I');

    }

}
