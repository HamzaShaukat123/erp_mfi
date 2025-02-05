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
use Carbon\Carbon;

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
                ->orderBy('pdc.pdc_id', 'desc')
                ->get();
        $acc = AC::where('status', 1)->orderBy('ac_name', 'asc')->get();

        return view('pdc.index',compact('jv1','acc'));
    }

    // public function show(string $id)
    // {
    //     // Retrieve the record with joined debit and credit account details
    //     $jv1 = pdc::where('pdc.pdc_id', $id)
    //             ->join('ac as d_ac', 'd_ac.ac_code', '=', 'pdc.ac_dr_sid')
    //             ->join('ac as c_ac', 'c_ac.ac_code', '=', 'pdc.ac_cr_sid')
    //             ->select('pdc.*', 
    //             'd_ac.ac_name as debit_account', 
    //             'c_ac.ac_name as credit_account')
    //             ->first();
    
    //     // Point to the correct Blade view file: show.blade.php
    //     return view('pdc_id.show', compact('jv1'));
    // }
    
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
                $pdc_att = new pdc_att();
                $pdc_att->pdc_id = $latest_jv1['pdc_id'];
                $extension = $file->getClientOriginalExtension();
                $pdc_att->att_path = $this->pdcDoc($file,$extension);
                $pdc_att->save();
            }
        }
        return redirect()->route('all-pdc');
    }


    public function storeMultiple(Request $request)
    {
        if ($request->has('items')) {
            for ($i = 0; $i < $request->items; $i++) {
                if (filled($request->ac_dr_sid[$i])) {
                    $pdc = new pdc();
                    $pdc->date = $request->date[$i];
                    $pdc->ac_dr_sid = $request->ac_dr_sid[$i];
                    $pdc->ac_cr_sid = $request->ac_cr_sid[$i];
                    $pdc->remarks = $request->remarks[$i];
                    $pdc->bankname = $request->bankname[$i];
                    $pdc->instrumentnumber = $request->instrumentnumber[$i];
                    $pdc->chqdate = $request->chqdate[$i];
                    $pdc->amount = $request->amount[$i];
                    $pdc->status = 1;
                    $pdc->created_by = session('user_id');
                    $pdc->save();
                    
                    // Handle attachments
                    if ($request->hasFile("att.$i")) {
                        $files = $request->file("att.$i");
                        foreach ($files as $file) {
                            $pdc_att = new pdc_att();
                            $pdc_att->pdc_id = $pdc->id;
                            $extension = $file->getClientOriginalExtension();
                            $pdc_att->att_path = $this->pdcDoc($file, $extension);
                            $pdc_att->save();
                        }
                    }
                }
            }
        }
        return redirect()->route('all-pdc');
    }

   

    public function create(Request $request)
    {
        
        $acc = AC::where('status', 1)->orderBy('ac_name', 'asc')->get();
        return view('pdc.create',compact('acc'));
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
            
            // pdc_att::where('jv1_id', $request->update_pdc_id)->delete();
            $files = $request->file('update_att');
            foreach ($files as $file)
            {
                $pdc_att = new pdc_att();
                $pdc_att->pdc_id =  $request->update_pdc_id;
                $extension = $file->getClientOriginalExtension();
                $pdc_att->att_path = $this->pdcDoc($file,$extension);
                $pdc_att->save();
            }
        }

        return redirect()->route('all-pdc');
    }

    public function addAtt(Request $request)
    {
        $pdc_id=$request->att_id;

        if($request->hasFile('addAtt')){
            $files = $request->file('addAtt');
            foreach ($files as $file)
            {
                $pdc_att = new pdc_att();
                $pdc_att->created_by = session('user_id');                
                $pdc_att->pdc_id = $pdc_id;
                $extension = $file->getClientOriginalExtension();
                $pdc_att->att_path = $this->pdcDoc($file,$extension);
                $pdc_att->save();
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
        $pdc_atts = pdc_att::where('pdc_id', $request->id)->get();
        return $pdc_atts;
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
        $doc=pdc_att::where('att_id', $id)->select('att_path')->first();
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
            $pdc_att = pdc_att::where('att_id', $id)->delete();
            return response()->json(['message' => 'File deleted successfully.']);
        } else {
            return response()->json(['message' => 'File not found.'], 404);
        }
    }

    public function ShowMultiple(Request $request)
    {
        // Convert the comma-separated string into an array
        $pdcIds = explode(',', $request->input('selected_pdc'));
    
        if (empty($pdcIds) || $pdcIds[0] == "") {
            return back()->with('error', 'No PDC selected.');
        }
    
        // Fetch records with join logic
        $pdcRecords = PDC::whereIn('pdc.pdc_id', $pdcIds)
            ->join('ac as d_ac', 'd_ac.ac_code', '=', 'pdc.ac_dr_sid')
            ->join('ac as c_ac', 'c_ac.ac_code', '=', 'pdc.ac_cr_sid')
            ->select('pdc.*', 
                'd_ac.ac_name as debit_account', 
                'c_ac.ac_name as credit_account'
            )
            ->get();
    
        // Return a Blade view with the fetched records
        return view('pdc.show', compact('pdcRecords', 'pdcIds'));
    }
    


    public function printPDC(Request $request)
    {
        // Get the selected PDC IDs from the request
        $pdcIds = explode(',', $request->query('selected_pdc'));
    
        if (empty($pdcIds) || $pdcIds[0] == "") {
            return back()->with('error', 'No PDC selected.');
        }
    
        // Fetch the records using the PDC IDs
        $pdcRecords = PDC::whereIn('pdc.pdc_id', $pdcIds)
            ->join('ac as d_ac', 'd_ac.ac_code', '=', 'pdc.ac_dr_sid')
            ->join('ac as c_ac', 'c_ac.ac_code', '=', 'pdc.ac_cr_sid')
            ->select('pdc.*', 'd_ac.ac_name as debit_account', 'c_ac.ac_name as credit_account')
        ->get();

     // Get and format current and report dates
        $currentDate = Carbon::now()->format('d-m-y');

        // Initialize PDF
        $pdf = new MyPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('MFI');
        $pdf->SetTitle('PDC Report');
        $pdf->SetSubject('PDC Report');
        $pdf->SetKeywords('PDC, Report, TCPDF, PDF');
        $pdf->setPageOrientation('L');
        $pdf->AddPage();

        // Document header
        $heading = '
        <table style="width:100%; border-bottom:2px solid #17365D;">
            <tr>
                <td style="font-size:22px; font-weight:bold; color:#17365D; width:70%;">
                    PDC Report
                </td>
                <td style="font-size:22px; font-weight:bold; color:#17365D;  width:30%;">
                    Print Date: <span style="color:black;">' . htmlspecialchars($currentDate) . '</span>
                </td>
            </tr>
        </table>';
    
        $pdf->writeHTML($heading, true, false, true, false, '');
    

        // Start of the Table with PDC data
        $html = '<table border="1" style="border-collapse: collapse;text-align:center">
                    <tr>
                        <th style="width:7%;color:#17365D;font-weight:bold;">SR</th>
                        <th style="width:17%;color:#17365D;font-weight:bold;">Account Debit</th>
                        <th style="width:17%;color:#17365D;font-weight:bold;">Account Credit</th>
                        <th style="width:21%;color:#17365D;font-weight:bold;">Remarks</th>
                        <th style="width:15%;color:#17365D;font-weight:bold;">Instrument</th>
                        <th style="width:11%;color:#17365D;font-weight:bold;">Chq Date</th>
                        <th style="width:12%;color:#17365D;font-weight:bold;">Amount</th>
                    </tr>';

        $totalAmount = 0;
        $count = 1; // Initialize the count variable

        foreach ($pdcRecords as $pdc) {
            $bgColor = ($count % 2 == 0) ? '#f1f1f1' : '#ffffff';
            $html .= '  <tr style="background-color:' . $bgColor . ';">
                            <td>' . $count . '</td>
                            <td>' . $pdc->debit_account . '</td>
                            <td>' . $pdc->credit_account . '</td>
                            <td>' . $pdc->remarks . ' ' . $pdc->bankname . '</td>
                            <td>' . $pdc->instrumentnumber . '</td>
                            <td>' . \Carbon\Carbon::parse($pdc->chqdate)->format('d-m-y') . '</td>
                            <td>' . number_format($pdc->amount, 0) . '</td>
                        </tr>';

            $totalAmount += $pdc->amount;
            $count++;
        }

        // Convert total amount to words
        $num_to_words = $pdf->convertCurrencyToWords(round($totalAmount));

        $html .= '
        <tr style="background-color:#d9edf7; font-weight:bold;">
            <td colspan="4" style="text-align:center; font-style:italic; padding:10px;">' . htmlspecialchars($num_to_words) . '</td>
            <td colspan="2" style="text-align:right;">Total Amount:</td>
            <td style="font-weight:bold; text-align:center;">' . number_format($totalAmount, 0) . '</td>
        </tr>';

        $html .= '</table>';
        $pdf->writeHTML($html, true, false, true, false, '');

        // Filename and Output
        $filename = "pdc_report.pdf";
        $pdf->Output($filename, 'I');

    }
    
}
