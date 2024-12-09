<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AC;
use App\Models\activites10_gen_ac;
use App\Exports\DailyRegJV1Export;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\myPDF;
use Carbon\Carbon;

class RptDailyRegJV1Controller extends Controller
{
    public function jv1(Request $request){
        $activites10_gen_ac = activites10_gen_ac::whereBetween('Date', [$request->fromDate, $request->toDate])
        ->join('ac as dr_acc','dr_acc.ac_code','=','activites10_gen_ac.ac_dr_sid')
        ->join('ac as cr_acc','cr_acc.ac_code','=','activites10_gen_ac.ac_cr_sid')
        ->select('activites10_gen_ac.*','dr_acc.ac_name as Debit_Acc','cr_acc.ac_name as Credit_Acc') 
        ->get();

        return $activites10_gen_ac;
    }

    public function jv1Excel(Request $request)
    {
        $activites10_gen_ac = activites10_gen_ac::whereBetween('Date', [$request->fromDate, $request->toDate])
        ->join('ac as dr_acc','dr_acc.ac_code','=','activites10_gen_ac.ac_dr_sid')
        ->join('ac as cr_acc','cr_acc.ac_code','=','activites10_gen_ac.ac_cr_sid')
        ->select('activites10_gen_ac.*','dr_acc.ac_name as Debit_Acc','cr_acc.ac_name as Credit_Acc') 
        ->get();

        $fromDate = \Carbon\Carbon::parse($request->fromDate)->format('Y-m-d');
        $toDate = \Carbon\Carbon::parse($request->toDate)->format('Y-m-d');
        
        // Construct the filename
        $filename = "daily_reg_jv1_report_from_{$fromDate}_to_{$toDate}.xlsx";

        // Return the download response with the dynamic filename
        return Excel::download(new DailyRegJV1Export($activites10_gen_ac), $filename);
    }

    public function jv1Report(Request $request)
    {
        // Validate the request
        $request->validate([
            'fromDate' => 'required|date',
            'toDate' => 'required|date',
            'outputType' => 'required|in:download,view',
        ]);
    
        // Retrieve data from the database
        $activites10_gen_ac = activites10_gen_ac::whereBetween('Date', [$request->fromDate, $request->toDate])
        ->join('ac as dr_acc','dr_acc.ac_code','=','activites10_gen_ac.ac_dr_sid')
        ->join('ac as cr_acc','cr_acc.ac_code','=','activites10_gen_ac.ac_cr_sid')
        ->select('activites10_gen_ac.*','dr_acc.ac_name as Debit_Acc','cr_acc.ac_name as Credit_Acc') 
        ->get();
    
        // Check if data exists
        if ($activites10_gen_ac->isEmpty()) {
            return response()->json(['message' => 'No records found for the selected date range.'], 404);
        }
    
        // Generate the PDF
        return $this->jv1generatePDF($activites10_gen_ac, $request);
    }

    private function jv1generatePDF($activites10_gen_ac, Request $request)
    {
        $currentDate = Carbon::now();
        $formattedDate = $currentDate->format('d-m-y');
        $formattedFromDate = Carbon::parse($request->fromDate)->format('d-m-y');
        $formattedToDate = Carbon::parse($request->toDate)->format('d-m-y');
    
        $pdf = new MyPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('MFI');
        $pdf->SetTitle('Daily Register JV 1 ' . $request->acc_id);
        $pdf->SetSubject('Daily Register JV 1');
        $pdf->SetKeywords('Daily Register JV 1, TCPDF, PDF');
        $pdf->setPageOrientation('P');
    
        // Add a page and set padding
        $pdf->AddPage();
        $pdf->setCellPadding(1.2);
    
        // Report heading
        $heading = '<h1 style="font-size:20px;text-align:center; font-style:italic;text-decoration:underline;color:#17365D">Daily Register JV 1</h1>';
        $pdf->writeHTML($heading, true, false, true, false, '');
    
        // Header details
        $htmlHeaderDetails = '
        <table style="border:1px solid #000; width:100%; padding:6px; border-collapse:collapse;">
            <tr>
                <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; border-bottom:1px solid #000;border-left:1px solid #000; width:33%;">
                    From Date: <span style="color:black;">' . $formattedFromDate . '</span>
                </td>
                <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left;border-left:1px solid #000; width:34%;">
                    To Date: <span style="color:black;">' . $formattedToDate . '</span>
                </td>
                <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; border-bottom:1px solid #000;border-left:1px solid #000; width:33%;">
                    Print Date: <span style="color:black;">' . $formattedDate . '</span>
                </td>
            </tr>
        </table>';
        $pdf->writeHTML($htmlHeaderDetails, true, false, true, false, '');

        // Table headers
        $tableHeader = '<tr>
                    <th style="width:7%;color:#17365D;font-weight:bold;">S/No</th>
                    <th style="width:13%;color:#17365D;font-weight:bold;">R/No.</th>
                    <th style="width:12%;color:#17365D;font-weight:bold;">Date</th>
                    <th style="width:14%;color:#17365D;font-weight:bold;">Debit</th>
                    <th style="width:14%;color:#17365D;font-weight:bold;">Credit</th>
                    <th style="width:25%;color:#17365D;font-weight:bold;">Remarks</th>
                    <th style="width:15%;color:#17365D;font-weight:bold;">Amount</th>
                </tr>';

        // Start the table
        $html = '<table border="1" style="border-collapse: collapse;text-align:center">';
        $html .= $tableHeader;

        $count = 1;
        $totalAmount = 0;

        foreach ($activites10_gen_ac as $items) {
            // Check if a new page is needed
            if ($pdf->getY() > 250) { // Adjust 250 based on your page margins
                $html .= '</table>'; // Close the current table
                $pdf->writeHTML($html, true, false, true, false, '');
                $pdf->AddPage(); // Add a new page
                $html = '<table border="1" style="border-collapse: collapse;text-align:center">';
                $html .= $tableHeader; // Re-add table header
            }

            // Add table rows
            $bgColor = ($count % 2 == 0) ? '#f1f1f1' : '#ffffff';
            $html .= '<tr style="background-color:' . $bgColor . ';">
                        <td>' . $count . '</td>
                        <td>' . $items['prefix'] . '' . $items['auto_lager'] . '</td>
                        <td>' . Carbon::createFromFormat('Y-m-d', $items['Date'])->format('d-m-y') . '</td>
                        <td>' . $items['Debit_Acc'] . '</td>
                        <td>' . $items['Credit_Acc'] . '</td>
                        <td>' . $items['remarks'] . '</td>
                        <td>' . number_format($items['Amount'], 0) . '</td>
                    </tr>';

            $totalAmount += $items['Amount'];
            $count++;
        }

        // Add totals row
        $html .= '<tr style="background-color:#d9edf7; font-weight:bold;">
                    <td colspan="6" style="text-align:right;">Total:</td>
                    <td style="width:15%;">' . number_format($totalAmount, 0) . '</td>
                </tr>';
        $html .= '</table>';
        $pdf->writeHTML($html, true, false, true, false, '');
    
        // Prepare filename for the PDF
        $fromDate = Carbon::parse($request->fromDate)->format('Y-m-d');
        $toDate = Carbon::parse($request->toDate)->format('Y-m-d');

        $filename = "daily_reg_jv1_report_from_{$fromDate}_to_{$toDate}.pdf";

        // Determine output type
        if ($request->outputType === 'download') {
            $pdf->Output($filename, 'D'); // For download
        } else {
            $pdf->Output($filename, 'I'); // For inline view
        }
    }
}
