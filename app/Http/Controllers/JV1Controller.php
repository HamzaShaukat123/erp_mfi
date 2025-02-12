<?php

namespace App\Http\Controllers;

use App\Services\myPDF;
use App\Models\AC;
use NumberFormatter;
use App\Models\jv1_att;
use App\Models\jvsingel;
use App\Models\pdc;
use App\Traits\SaveImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class JV1Controller extends Controller
{
    use SaveImage;

    public function index()
    {
        $jv1 = jvsingel::where('jvsingel.status', 1)
                ->leftjoin('ac as d_ac', 'd_ac.ac_code', '=', 'jvsingel.ac_dr_sid')
                ->join('ac as c_ac', 'c_ac.ac_code', '=', 'jvsingel.ac_cr_sid')
                ->select('jvsingel.*', 
                'd_ac.ac_name as debit_account', 
                'c_ac.ac_name as credit_account')
                ->get();
        $acc = AC::where('status', 1)->orderBy('ac_name', 'asc')->get();

        return view('jv1.jv1',compact('jv1','acc'));
    }

    public function show(string $id)
    {
        // Retrieve the record with joined debit and credit account details
        $jv1 = jvsingel::where('jvsingel.auto_lager', $id)
                ->join('ac as d_ac', 'd_ac.ac_code', '=', 'jvsingel.ac_dr_sid')
                ->join('ac as c_ac', 'c_ac.ac_code', '=', 'jvsingel.ac_cr_sid')
                ->select('jvsingel.*', 
                'd_ac.ac_name as debit_account', 
                'c_ac.ac_name as credit_account')
                ->first();
    
        // Point to the correct Blade view file: show.blade.php
        return view('jv1.jv1-show', compact('jv1'));
    }
    
    public function store(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'ac_dr_sid' => 'required',
        //     'ac_cr_sid' => 'required',
        // ]);
        
        // if ($validator->fails()) {
        //     return response()->json(['errors' => $validator->errors()], 422);
        // }
    
        // $jv1 = new jvsingel();
        // $jv1->created_by = session('user_id');
    
        // if ($request->has('ac_dr_sid') && $request->ac_dr_sid) {
        //     $jv1->ac_dr_sid = $request->ac_dr_sid;
        // }
        // if ($request->has('ac_cr_sid') && $request->ac_cr_sid) {
        //     $jv1->ac_cr_sid = $request->ac_cr_sid;
        // }
        // if ($request->has('amount') && ($request->amount || $request->amount == 0)) {
        //     $jv1->amount = $request->amount;
        // }
        // if ($request->has('date') && $request->date) {
        //     $jv1->date = $request->date;
        // }
        // if ($request->has('remarks') && ($request->remarks || empty($request->remarks))) {
        //     $jv1->remarks = $request->remarks;
        // }
        // $jv1->save();
    
        // // Fetch the latest jv1 entry for reference
        // $latest_jv1 = jvsingel::latest()->first();
    
        // if ($request->hasFile('att')) {
        //     $files = $request->file('att');
        //     foreach ($files as $file) {
        //         $jv1_att = new jv1_att();
        //         $jv1_att->jv1_id = $latest_jv1['auto_lager'];
        //         $extension = $file->getClientOriginalExtension();
        //         $jv1_att->att_path = $this->jv1Doc($file, $extension);
        //         $jv1_att->save();
        //     }
        // }
     

        if ($request->has('isInduced') && $request->isInduced == 1) {
            // Fetch the latest JV1 record
            $latest_jv1 = jvsingel::latest()->first();
        
            // Find PDC using pdc_id from the request
            $pur_2_id = Pdc::where('pdc_id', $request->pdc_id)->first();
            
            if ($pur_2_id && $latest_jv1) {
                // Update the PDC table with voch_id and voch_prefix
                Pdc::where('pdc_id', $request->pdc_id)->update([
                    'voch_id' => $latest_jv1->auto_lager,
                    'voch_prefix' => $latest_jv1->prefix,
                ]);
            }
        
            // Validate input for induced transactions
            $validator = Validator::make($request->all(), [
                'ac_dr_sid_hidden' => 'required',
                'ac_cr_sid_hidden' => 'required',
            ]);
        
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
        
            // Create new jvsingel record
            $jv1 = new jvsingel();
            $jv1->created_by = session('user_id');
            $jv1->ac_dr_sid = $request->ac_dr_sid_hidden;
            $jv1->ac_cr_sid = $request->ac_cr_sid_hidden;
            $jv1->amount = $request->amount ?? 0;
            $jv1->date = $request->date ?? null;
            $jv1->remarks = $request->remarks ?? null;
            $jv1->save();
        } else 
        {
            // Validate input for normal transactions
            $validator = Validator::make($request->all(), [
                'ac_dr_sid' => 'required',
                'ac_cr_sid' => 'required',
            ]);
        
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
        
            // Create new jvsingel record
            $jv1 = new jvsingel();
            $jv1->created_by = session('user_id');
            $jv1->ac_dr_sid = $request->ac_dr_sid;
            $jv1->ac_cr_sid = $request->ac_cr_sid;
            $jv1->amount = $request->amount ?? 0;
            $jv1->date = $request->date ?? null;
            $jv1->remarks = $request->remarks ?? null;
            $jv1->save();
        }
        
        // Fetch the latest jv1 entry for reference
        $latest_jv1 = jvsingel::latest()->first();
        
        if ($request->hasFile('att')) {
            foreach ($request->file('att') as $file) {
                $jv1_att = new jv1_att();
                $jv1_att->jv1_id = $latest_jv1->auto_lager;
                $jv1_att->att_path = $this->jv1Doc($file, $file->getClientOriginalExtension());
                $jv1_att->save();
            }
        }
        
        // Redirect if no induced flag is set
        return redirect()->route('all-jv1');
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
        $jv1 = jvsingel::where('auto_lager', $request->update_auto_lager)->get()->first();

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
        if ($request->has('update_remarks') && $request->update_remarks OR empty($request->update_remarks))  {
            $jv1->remarks=$request->update_remarks;
        }
    
        jvsingel::where('auto_lager', $request->update_auto_lager)->update([
            'ac_dr_sid'=>$jv1->ac_dr_sid,
            'ac_cr_sid'=>$jv1->ac_cr_sid,
            'amount'=>$jv1->amount,
            'date'=>$jv1->date,
            'remarks'=>$jv1->remarks,
            'updated_by' => session('user_id'),
        ]);

        if($request->hasFile('update_att')){
            
            // jv1_att::where('jv1_id', $request->update_auto_lager)->delete();
            $files = $request->file('update_att');
            foreach ($files as $file)
            {
                $jv1_att = new jv1_att();
                $jv1_att->jv1_id =  $request->update_auto_lager;
                $extension = $file->getClientOriginalExtension();
                $jv1_att->att_path = $this->jv1Doc($file,$extension);
                $jv1_att->save();
            }
        }

        return redirect()->route('all-jv1');
    }

    public function addAtt(Request $request)
    {
        $jv1_id=$request->att_id;

        if($request->hasFile('addAtt')){
            $files = $request->file('addAtt');
            foreach ($files as $file)
            {
                $jv1_att = new jv1_att();
                $jv1_att->created_by = session('user_id');                
                $jv1_att->jv1_id = $jv1_id;
                $extension = $file->getClientOriginalExtension();
                $jv1_att->att_path = $this->jv1Doc($file,$extension);
                $jv1_att->save();
            }
        }
        return redirect()->route('all-jv1');

    }

    public function destroy(Request $request)
    {
        $jv1 = jvsingel::where('auto_lager', $request->delete_auto_lager)->update([
            'status' => '0',
            'updated_by' => session('user_id'),
        ]);
        return redirect()->route('all-jv1');
    }

    public function getAttachements(Request $request)
    {
        $jv1_atts = jv1_att::where('jv1_id', $request->id)->get();
        return $jv1_atts;
    }

    public function getJVDetails(Request $request)
    {
        $jv1_details = jvsingel::where('auto_lager', $request->id)->get()->first();
        return $jv1_details;
    }

    public function view($id)
    {
        $doc=jv1_att::where('att_id', $id)->select('att_path')->first();
        $filePath = public_path($doc['att_path']);
        if (file_exists($filePath)) {
            return Response::file($filePath);
        } 
    }

    public function downloadAtt($id)
    {
        $doc=jv1_att::where('att_id', $id)->select('att_path')->first();
        $filePath = public_path($doc['att_path']);
        if (file_exists($filePath)) {
            return Response::download($filePath);
        } 
    }

    public function deleteAtt($id)
    {
        $doc=jv1_att::where('att_id', $id)->select('att_path')->first();
        $filePath = public_path($doc['att_path']);

        if (File::exists($filePath)) {
            File::delete($filePath);
            $jv1_att = jv1_att::where('att_id', $id)->delete();
            return response()->json(['message' => 'File deleted successfully.']);
        } else {
            return response()->json(['message' => 'File not found.'], 404);
        }
    }
    

    public function getpdc()
    {
        $unclosed_inv = pdc::where('pdc.status', 1)
        ->whereNull('pdc.voch_id')
        ->leftjoin('ac as d_ac', 'd_ac.ac_code', '=', 'pdc.ac_dr_sid')
        ->join('ac as c_ac', 'c_ac.ac_code', '=', 'pdc.ac_cr_sid')
        ->select('pdc.*', 
        'd_ac.ac_name as debit_account', 
        'c_ac.ac_name as credit_account')
        ->orderBy('pdc.chqdate', 'asc') 
        ->orderBy('pdc.pdc_id', 'asc')
        ->get();

        return $unclosed_inv;
    }
    
    public function getItems($id)
    {
        try {
            \Log::info("Fetching PDC data for ID: " . $id); // Debugging log
    
            // Check if data exists
            $pdc = Pdc::where('pdc_id', $id)->first();
    
            if (!$pdc) {
                \Log::warning("No PDC record found for ID: " . $id);
                return response()->json(['error' => 'PDC ID not found'], 404);
            }
    
            \Log::info("PDC data found", ['data' => $pdc]);
    
            return response()->json(['pur2' => $pdc]);
    
        } catch (\Exception $e) {
            \Log::error("Error fetching PDC data: " . $e->getMessage());
            return response()->json(['error' => 'Server Error'], 500);
        }
    }
    
    
    

    public function print($id)
    {

        $jv1 = jvsingel::where('jvsingel.auto_lager', $id)
        ->join('ac as d_ac', 'd_ac.ac_code', '=', 'jvsingel.ac_dr_sid')
        ->join('ac as c_ac', 'c_ac.ac_code', '=', 'jvsingel.ac_cr_sid')
        ->select('jvsingel.*', 
        'd_ac.ac_name as debit_account', 
        'c_ac.ac_name as credit_account')
        ->first();

        $pdf = new MyPDF();

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('MFI');
        $pdf->SetTitle('JV1 # '.$jv1['auto_lager']);
        $pdf->SetSubject('JV1 # '.$jv1['auto_lager']);
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
        $html .= '<td style="font-size:12px;font-weight:bold;color:#17365D;font-family:poppins"> Voucher No: <span style="text-decoration: underline;color:black;">'.$jv1['auto_lager'].'</span></td>';
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

        $pdf->Output('jv1_'.$jv1['auto_lager'].'.pdf', 'I');

    }

}
