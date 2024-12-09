<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\balance_sub_head;
use App\Exports\ACGroupSHOAExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\myPDF;
use Carbon\Carbon;


class RptAccGrpSHOAController extends Controller
{
    public function shoa(Request $request){
        $balance_sub_head = balance_sub_head::where('sub',$request->acc_id)
        ->orderBy('ac_name', 'asc')
        ->get();

        return $balance_sub_head;
    }

    public function shoaExcel(Request $request)
    {
        $balance_sub_head = balance_sub_head::where('sub',$request->acc_id)
        ->select('ac_code', 'ac_name', 'address', 'Debit', 'Credit')
        ->get();

        $accId = $request->acc_id;
        
        // Construct the filename
        $filename = "balance_sub_head_of_acc_{$accId}.xlsx";

        // Return the download response with the dynamic filename
        return Excel::download(new ACGroupSHOAExport($balance_sub_head), $filename);
    }

    public function shoaReport(Request $request)
    {
        // Validate the request
        $request->validate([
            'acc_id' => 'required',
            'outputType' => 'required|in:download,view',
        ]);
    
        // Retrieve data from the database
        $balance_sub_head = balance_sub_head::where('balance_sub_head.sub',$request->acc_id)
        ->join('sub_head_of_acc as shoa','shoa.id','=','balance_sub_head.sub')
        ->select('balance_sub_head.*','shoa.sub as shoa_name')
        ->orderBy('ac_name', 'asc')
        ->get();
    
        // Check if data exists
        if ($balance_sub_head->isEmpty()) {
            return response()->json(['message' => 'No records found for the Account.'], 404);
        }
    
        // Generate the PDF
        return $this->shoageneratePDF($balance_sub_head, $request);
    }

    private function shoageneratePDF($balance_sub_head, Request $request)
    {
        $currentDate = Carbon::now();
        $formattedDate = $currentDate->format('d-m-y');
    
        $pdf = new MyPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('MFI');
        $pdf->SetTitle('Sub Head Of Acc Report-' . $balance_sub_head[0]['shoa_name']);
        $pdf->SetSubject('Sub Head Of Acc Report');
        $pdf->SetKeywords('Sub Head Of Acc Report, TCPDF, PDF');
        $pdf->setPageOrientation('P');
    
        // Add a page and set padding
        $pdf->AddPage();
        $pdf->setCellPadding(1.2);
    
        // Report heading
        $heading = '<h1 style="font-size:20px;text-align:center; font-style:italic;text-decoration:underline;color:#17365D">Sub Head Of Account Report</h1>';
        $pdf->writeHTML($heading, true, false, true, false, '');
    
        // Header details
        $html = '
        <table style="border:1px solid #000; width:100%; padding:6px; border-collapse:collapse;">
            <tr>
                <td style="font-size:12px; font-weight:bold; color:#17365D; padding:5px 10px; border-bottom:1px solid #000; width:70%;">
                    Group Name: <span style="color:black;">'.$balance_sub_head[0]['shoa_name'].'</span>
                </td>
                <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; padding:5px 10px; border-bottom:1px solid #000;border-left:1px solid #000; width:30%;">
                    Date: <span style="color:black;">' . htmlspecialchars($formattedDate) . '</span>
                </td>
            </tr>
            
        </table>';

        $pdf->writeHTML($html, true, false, true, false, '');

    
        // Table header for data
        $html = '
            <table border="1" style="border-collapse: collapse; text-align: center;">
                <tr>
                    <th style="width:7%;color:#17365D;font-weight:bold;">S/No</th>
                    <th style="width:8%;color:#17365D;font-weight:bold;">Code</th>
                    <th style="width:32%;color:#17365D;font-weight:bold;">Account Name</th>
                    <th style="width:25%;color:#17365D;font-weight:bold;">Address/Phone</th>
                    <th style="width:14%;color:#17365D;font-weight:bold;">Debit</th>
                    <th style="width:14%;color:#17365D;font-weight:bold;">Credit</th>
                </tr>';
    
        // Iterate through items and add rows
        $count = 1;
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($balance_sub_head as $item) {
            $backgroundColor = ($count % 2 == 0) ? '#f1f1f1' : '#ffffff'; // Alternating row colors
    
            $html .= '
                <tr style="background-color:' . $backgroundColor . ';">
                    <td style="width:7%;">' . $count . '</td>
                    <td style="width:8%;">' . $item['ac_code']. '</td>
                    <td style="width:32%;">' . $item['ac_name'] . '</td>
                    <td style="width:25%;">' . $item['address'] . '' . $item['phone'] . '</td>
                    <td style="width:14%;">' . $item['Debit'] . '</td>
                    <td style="width:14%;">' . $item['Credit'] . '</td>
                </tr>';
            
            $totalDebit += $item['Debit']; // Accumulate total quantity
            $totalCredit += $item['Credit']; // Accumulate total quantity
            $count++;
        }
    
        // Add totals row
        $html .= '
        <tr style="background-color:#d9edf7; font-weight:bold;">
            <td colspan="4" style="text-align:right;">Total:</td>
            <td style="width:14%;">' . number_format($totalDebit, 0) . '</td>
            <td style="width:14%;">' . number_format($totalCredit, 0) . '</td>
        </tr>';

        // Calculate balance and add balance row
        $balance = $totalDebit + $totalCredit;
        $html .= '
        <tr style="background-color:#d2edc7; font-weight:bold;">
            <td colspan="4" style="text-align:right;">Balance:</td>
            <td colspan="2" style="text-align:center;">' . number_format($balance, 0) . '</td>
        </tr>';

            
        $html .= '</table>';
        $pdf->writeHTML($html, true, false, true, false, '');
        
        $accId = $request->acc_id;
        $filename = "sub_head_bal_report_{$balance_sub_head[0]['shoa_name']}.pdf";

        // Determine output type
        if ($request->outputType === 'download') {
            $pdf->Output($filename, 'D'); // For download
        } else {
            $pdf->Output($filename, 'I'); // For inline view
        }
    }
}
