<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AC;
use App\Exports\Purchase2Export;
use App\Models\pipe_pur_by_account;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\myPDF;
use Carbon\Carbon;

class RptAccNamePur2Controller extends Controller
{
    public function purchase2(Request $request){
        $pipe_pur_by_account = pipe_pur_by_account::where('ac1',$request->acc_id)
        ->whereBetween('date', [$request->fromDate, $request->toDate])
        ->orderBy('date', 'asc')
        ->get();

        return $pipe_pur_by_account;
    }

    public function purchase2Excel(Request $request)
    {
        $pipe_pur_by_account = pipe_pur_by_account::where('ac1', $request->acc_id)
            ->whereBetween('date', [$request->fromDate, $request->toDate])
            ->get();

        $accId = $request->acc_id;
        $fromDate = \Carbon\Carbon::parse($request->fromDate)->format('Y-m-d');
        $toDate = \Carbon\Carbon::parse($request->toDate)->format('Y-m-d');
        
        // Construct the filename
        $filename = "purchase2_report_{$accId}_from_{$fromDate}_to_{$toDate}.xlsx";

        // Return the download response with the dynamic filename
        return Excel::download(new Purchase2Export($pipe_pur_by_account), $filename);
    }

    public function purchase2PDF(Request $request)
    {
        $pipe_pur_by_account = pipe_pur_by_account::where('ac1', $request->acc_id)
        ->whereBetween('date', [$request->fromDate, $request->toDate])
        ->leftjoin('ac','ac.ac_code','=','pipe_pur_by_account.ac1')
        ->select('pipe_pur_by_account.*', 'ac.ac_name', 'ac.remarks as ac_remarks') 
        ->orderBy('date', 'asc')
        ->get();

        
            // Get and format current and report dates
            $currentDate = Carbon::now()->format('d-m-y');
            $formattedFromDate = Carbon::createFromFormat('Y-m-d', $request->fromDate)->format('d-m-y');
            $formattedToDate = Carbon::createFromFormat('Y-m-d', $request->toDate)->format('d-m-y');
    
            // Initialize PDF
            $pdf = new MyPDF();
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('MFI');
            $pdf->SetTitle("Purchase 2 Report Of Account - {$pipe_pur_by_account[0]['ac_name']}");
            $pdf->SetSubject("Purchase 2 Report Of Account - {$pipe_pur_by_account[0]['ac_name']}");
            $pdf->SetKeywords('Purchase 2 Report, TCPDF, PDF');
            $pdf->setPageOrientation('P');
            $pdf->AddPage();
            $pdf->setCellPadding(1.2);
    
            // Document header
            $heading = '<h1 style="font-size:20px;text-align:center;font-style:italic;text-decoration:underline;color:#17365D">Purchase 2 Report Of Account</h1>';
            $pdf->writeHTML($heading, true, false, true, false, '');
    
            // Account Info Table
            $html = '
                <table style="border:1px solid #000; width:100%; padding:6px; border-collapse:collapse;">
                    <tr>
                        <td style="font-size:12px; font-weight:bold; color:#17365D; padding:5px 10px; border-bottom:1px solid #000; width:70%;">
                            Account Name: <span style="color:black;">' . htmlspecialchars($pipe_pur_by_account[0]['ac_name']) . '</span>
                        </td>
                        <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; padding:5px 10px; border-bottom:1px solid #000;border-left:1px solid #000; width:30%;">
                            Print Date: <span style="color:black;">' . htmlspecialchars($currentDate) . '</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:12px; font-weight:bold; color:#17365D; padding:5px 10px; border-bottom:1px solid #000; width:70%;">
                        Remarks: <span style="color:black;">' . htmlspecialchars($pipe_pur_by_account[0]['ac_remarks']) . '</span>
                        </td>
                        <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; padding:5px 10px; border-bottom:1px solid #000; border-left:1px solid #000;width:30%;">
                            From Date: <span style="color:black;">' . htmlspecialchars($formattedFromDate) . '</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:5px 10px; border-bottom:1px solid #000; width:70%;"></td>
                        <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; padding:5px 10px; border-bottom:1px solid #000;border-left:1px solid #000; width:30%;">
                            To Date: <span style="color:black;">' . htmlspecialchars($formattedToDate) . '</span>
                        </td>
                    </tr>
                </table>';
    
            $pdf->writeHTML($html, true, false, true, false, '');
    
    
            // Table Headers
            $html = '<table border="1" style="border-collapse: collapse;text-align:center">
                        <tr>
                            <th style="width:7%;color:#17365D;font-weight:bold;">S/No</th>
                            <th style="width:14%;color:#17365D;font-weight:bold;">Date</th>
                            <th style="width:12%;color:#17365D;font-weight:bold;">Inv No.</th>
                            <th style="width:12%;color:#17365D;font-weight:bold;">Mill Inv</th>
                            <th style="width:16%;color:#17365D;font-weight:bold;">Dipatch To</th>
                            <th style="width:10%;color:#17365D;font-weight:bold;">Sale Inv</th>
                            <th style="width:13%;color:#17365D;font-weight:bold;">Remarks</th>
                            <th style="width:16%;color:#17365D;font-weight:bold;">Amount</th>
                        </tr>';
                    // Table Rows
                $count = 1;
                $totalAmount = 0;
                foreach ($pipe_pur_by_account as $items) {
                    $bgColor = ($count % 2 == 0) ? '#f1f1f1' : '#ffffff';
                    $html .= '<tr style="background-color:' . $bgColor . ';">
                                    <td>' . $count . '</td>
                                    <td>' . Carbon::createFromFormat('Y-m-d', $items['date'])->format('d-m-y') . '</td>
                                    <td>' . $items['no'] . '</td>
                                    <td>' . $items['pur_ord_no'] . '</td>
                                    <td>' . $items['ac2'] . '</td>
                                    <td>' . $items['sal_inv'] . '</td>
                                    <td>' . $items['remarks'] . '</td>
                                    <td>' . number_format($items['cr_amt'], 0) . '</td>
                                </tr>';

                            $totalAmount += $items['cr_amt'];
                            $count++;
                    }
                // Add totals row
                $html .= '
                <tr style="background-color:#d9edf7; font-weight:bold;">
                    <td colspan="7" style="text-align:right;">Total:</td>
                    <td style="width:16%;">' . number_format($totalAmount, 0) . '</td>
                </tr>';
                
            $html .= '</table>';
            $pdf->writeHTML($html, true, false, true, false, '');

    
        
    
            // Filename and Output
        $filename = "pur2_report_{$pipe_pur_by_account[0]['ac_name']}_from_{$formattedFromDate}_to_{$formattedToDate}.pdf";
        $pdf->Output($filename, 'I');
    }

    public function purchase2Download(Request $request)
    {
        $pipe_pur_by_account = pipe_pur_by_account::where('ac1', $request->acc_id)
        ->whereBetween('date', [$request->fromDate, $request->toDate])
        ->leftjoin('ac','ac.ac_code','=','pipe_pur_by_account.ac1')
        ->select('pipe_pur_by_account.*', 'ac.ac_name', 'ac.remarks as ac_remarks') 
        ->orderBy('date', 'asc')
        ->get();

        
            // Get and format current and report dates
            $currentDate = Carbon::now()->format('d-m-y');
            $formattedFromDate = Carbon::createFromFormat('Y-m-d', $request->fromDate)->format('d-m-y');
            $formattedToDate = Carbon::createFromFormat('Y-m-d', $request->toDate)->format('d-m-y');
    
            // Initialize PDF
            $pdf = new MyPDF();
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('MFI');
            $pdf->SetTitle("Purchase 2 Report Of Account - {$pipe_pur_by_account[0]['ac_name']}");
            $pdf->SetSubject("Purchase 2 Report Of Account - {$pipe_pur_by_account[0]['ac_name']}");
            $pdf->SetKeywords('Purchase 2 Report, TCPDF, PDF');
            $pdf->setPageOrientation('P');
            $pdf->AddPage();
            $pdf->setCellPadding(1.2);
    
            // Document header
            $heading = '<h1 style="font-size:20px;text-align:center;font-style:italic;text-decoration:underline;color:#17365D">Purchase 2 Report Of Account</h1>';
            $pdf->writeHTML($heading, true, false, true, false, '');
    
            // Account Info Table
            $html = '
                <table style="border:1px solid #000; width:100%; padding:6px; border-collapse:collapse;">
                    <tr>
                        <td style="font-size:12px; font-weight:bold; color:#17365D; padding:5px 10px; border-bottom:1px solid #000; width:70%;">
                            Account Name: <span style="color:black;">' . htmlspecialchars($pipe_pur_by_account[0]['ac_name']) . '</span>
                        </td>
                        <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; padding:5px 10px; border-bottom:1px solid #000;border-left:1px solid #000; width:30%;">
                            Print Date: <span style="color:black;">' . htmlspecialchars($currentDate) . '</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:12px; font-weight:bold; color:#17365D; padding:5px 10px; border-bottom:1px solid #000; width:70%;">
                        Remarks: <span style="color:black;">' . htmlspecialchars($pipe_pur_by_account[0]['ac_remarks']) . '</span>
                        </td>
                        <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; padding:5px 10px; border-bottom:1px solid #000; border-left:1px solid #000;width:30%;">
                            From Date: <span style="color:black;">' . htmlspecialchars($formattedFromDate) . '</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:5px 10px; border-bottom:1px solid #000; width:70%;"></td>
                        <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; padding:5px 10px; border-bottom:1px solid #000;border-left:1px solid #000; width:30%;">
                            To Date: <span style="color:black;">' . htmlspecialchars($formattedToDate) . '</span>
                        </td>
                    </tr>
                </table>';
    
            $pdf->writeHTML($html, true, false, true, false, '');
    
    
            // Table Headers
            $html = '<table border="1" style="border-collapse: collapse;text-align:center">
                        <tr>
                            <th style="width:7%;color:#17365D;font-weight:bold;">S/No</th>
                            <th style="width:14%;color:#17365D;font-weight:bold;">Date</th>
                            <th style="width:12%;color:#17365D;font-weight:bold;">Inv No.</th>
                            <th style="width:12%;color:#17365D;font-weight:bold;">Mill Inv</th>
                            <th style="width:16%;color:#17365D;font-weight:bold;">Dipatch To</th>
                            <th style="width:10%;color:#17365D;font-weight:bold;">Sale Inv</th>
                            <th style="width:13%;color:#17365D;font-weight:bold;">Remarks</th>
                            <th style="width:16%;color:#17365D;font-weight:bold;">Amount</th>
                        </tr>';
                    // Table Rows
                $count = 1;
                $totalAmount = 0;
                foreach ($pipe_pur_by_account as $items) {
                    $bgColor = ($count % 2 == 0) ? '#f1f1f1' : '#ffffff';
                    $html .= '<tr style="background-color:' . $bgColor . ';">
                                    <td>' . $count . '</td>
                                    <td>' . Carbon::createFromFormat('Y-m-d', $items['date'])->format('d-m-y') . '</td>
                                    <td>' . $items['no'] . '</td>
                                    <td>' . $items['pur_ord_no'] . '</td>
                                    <td>' . $items['ac2'] . '</td>
                                    <td>' . $items['sal_inv'] . '</td>
                                    <td>' . $items['remarks'] . '</td>
                                    <td>' . number_format($items['cr_amt'], 0) . '</td>
                                </tr>';

                            $totalAmount += $items['cr_amt'];
                            $count++;
                    }
                // Add totals row
                $html .= '
                <tr style="background-color:#d9edf7; font-weight:bold;">
                    <td colspan="7" style="text-align:right;">Total:</td>
                    <td style="width:16%;">' . number_format($totalAmount, 0) . '</td>
                </tr>';
                
            $html .= '</table>';
            $pdf->writeHTML($html, true, false, true, false, '');

    
        
    
            // Filename and Output
        $filename = "pur2_report_{$pipe_pur_by_account[0]['ac_name']}_from_{$formattedFromDate}_to_{$formattedToDate}.pdf";
        $pdf->Output($filename, 'D');
    }

}
