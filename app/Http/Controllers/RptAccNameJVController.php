<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AC;
use App\Models\all_payments_by_party;
use App\Exports\VouchersExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\myPDF;
use Carbon\Carbon;

class RptAccNameJVController extends Controller
{

    public function jv(Request $request){
        $all_payments_by_party = all_payments_by_party::where('account_cod',$request->acc_id)
        ->whereBetween('jv_date', [$request->fromDate, $request->toDate])
        ->orderBy('jv_date', 'asc')
        ->get();

        return $all_payments_by_party;
    }

    public function jvExcel(Request $request)
    {
        $all_payments_by_party = all_payments_by_party::where('account_cod',$request->acc_id)
        ->whereBetween('jv_date', [$request->fromDate, $request->toDate])
        ->get();

        $accId = $request->acc_id;
        $fromDate = \Carbon\Carbon::parse($request->fromDate)->format('Y-m-d');
        $toDate = \Carbon\Carbon::parse($request->toDate)->format('Y-m-d');
        
        // Construct the filename
        $filename = "jv_report_{$accId}_from_{$fromDate}_to_{$toDate}.xlsx";

        // Return the download response with the dynamic filename
        return Excel::download(new VouchersExport($all_payments_by_party), $filename);
    }

    public function jvPDF(Request $request)

    {
        $all_payments_by_party = all_payments_by_party::where('account_cod', $request->acc_id)
        ->whereBetween('jv_date', [$request->fromDate, $request->toDate])
        ->leftjoin('ac','ac.ac_code','=','all_payments_by_party.account_cod')
        ->orderBy('jv_date', 'asc')
        ->get();

        
        // Get and format current and report dates
        $currentDate = Carbon::now()->format('d-m-y');
        $formattedFromDate = Carbon::createFromFormat('Y-m-d', $request->fromDate)->format('d-m-y');
        $formattedToDate = Carbon::createFromFormat('Y-m-d', $request->toDate)->format('d-m-y');

        // Initialize PDF
        $pdf = new MyPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('MFI');
        $pdf->SetTitle("Journal Voucher Report Of Account - {$all_payments_by_party[0]['ac_name']}");
        $pdf->SetSubject("Journal Voucher Report Of Account - {$all_payments_by_party[0]['ac_name']}");
        $pdf->SetKeywords('Journal Voucher Report, TCPDF, PDF');
        $pdf->setPageOrientation('P');
        $pdf->AddPage();
        $pdf->setCellPadding(1.2);

        // Document header
        $heading = '<h1 style="font-size:20px;text-align:center;font-style:italic;text-decoration:underline;color:#17365D">Journal Voucher Report Of Account</h1>';
        $pdf->writeHTML($heading, true, false, true, false, '');

        // Account Info Table
        $html = '
            <table style="border:1px solid #000; width:100%; padding:6px; border-collapse:collapse;">
                <tr>
                    <td style="font-size:12px; font-weight:bold; color:#17365D; padding:5px 10px; border-bottom:1px solid #000; width:70%;">
                        Account Name: <span style="color:black;">' . htmlspecialchars($all_payments_by_party[0]['ac_name']) . '</span>
                    </td>
                    <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; padding:5px 10px; border-bottom:1px solid #000;border-left:1px solid #000; width:30%;">
                        Print Date: <span style="color:black;">' . htmlspecialchars($currentDate) . '</span>
                    </td>
                </tr>
                <tr>
                    <td style="font-size:12px; font-weight:bold; color:#17365D; padding:5px 10px; border-bottom:1px solid #000; width:70%;">
                    Remarks: <span style="color:black;">' . htmlspecialchars($all_payments_by_party[0]['ac_remarks']) . '</span>
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
                        <th style="width:14%;color:#17365D;font-weight:bold;">Voucher</th>
                        <th style="width:16%;color:#17365D;font-weight:bold;">Account Name</th>
                        <th style="width:21%;color:#17365D;font-weight:bold;">Remarks</th>
                        <th style="width:14%;color:#17365D;font-weight:bold;">Debit</th>
                        <th style="width:14%;color:#17365D;font-weight:bold;">Credit</th>
                    </tr>';
                // Table Rows
            $count = 1;
            $totalAmount = 0;
            $totalAmount2 = 0;
            foreach ($all_payments_by_party as $items) {
                $bgColor = ($count % 2 == 0) ? '#f1f1f1' : '#ffffff';
                $html .= '<tr style="background-color:' . $bgColor . ';">
                                <td>' . $count . '</td>
                                <td>' . Carbon::createFromFormat('Y-m-d', $items['jv_date'])->format('d-m-y') . '</td>
                                <td>' . $items['entry_of'] . '-' . $items['auto_lager'] . '</td>
                                <td>' . $items['ac2'] . '</td>
                                <td>' . $items['Narration'] . '</td>
                                <td>' . number_format($items['Debit'], 0) . '</td>
                                <td>' . number_format($items['Credit'], 0) . '</td>
                            </tr>';
                    $totalAmount += $items['Debit'];
                    $totalAmount2 += $items['Credit'];
                    $count++;
                    }
            // Add totals row
            $html .= '
            <tr style="background-color:#d9edf7; font-weight:bold;">
                <td colspan="5" style="text-align:right;">Total:</td>
                <td style="width:14%;">' . number_format($totalAmount, 0) . '</td>
                <td style="width:14%;">' . number_format($totalAmount2, 0) . '</td>
            </tr>';
                
            $html .= '</table>';
            $pdf->writeHTML($html, true, false, true, false, '');

    
        
    
            // Filename and Output
        $filename = "jv_all_report_{$all_payments_by_party[0]['ac_name']}_from_{$formattedFromDate}_to_{$formattedToDate}.pdf";
        $pdf->Output($filename, 'I');
    }

    public function jvDownload(Request $request)
    {
        $all_payments_by_party = all_payments_by_party::where('account_cod', $request->acc_id)
        ->whereBetween('jv_date', [$request->fromDate, $request->toDate])
        ->leftjoin('ac','ac.ac_code','=','all_payments_by_party.account_cod')
        ->orderBy('jv_date', 'asc')
        ->get();

        
        // Get and format current and report dates
        $currentDate = Carbon::now()->format('d-m-y');
        $formattedFromDate = Carbon::createFromFormat('Y-m-d', $request->fromDate)->format('d-m-y');
        $formattedToDate = Carbon::createFromFormat('Y-m-d', $request->toDate)->format('d-m-y');

        // Initialize PDF
        $pdf = new MyPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('MFI');
        $pdf->SetTitle("Journal Voucher Report Of Account - {$all_payments_by_party[0]['ac_name']}");
        $pdf->SetSubject("Journal Voucher Report Of Account - {$all_payments_by_party[0]['ac_name']}");
        $pdf->SetKeywords('Journal Voucher Report, TCPDF, PDF');
        $pdf->setPageOrientation('P');
        $pdf->AddPage();
        $pdf->setCellPadding(1.2);

        // Document header
        $heading = '<h1 style="font-size:20px;text-align:center;font-style:italic;text-decoration:underline;color:#17365D">Journal Voucher Report Of Account</h1>';
        $pdf->writeHTML($heading, true, false, true, false, '');

        // Account Info Table
        $html = '
            <table style="border:1px solid #000; width:100%; padding:6px; border-collapse:collapse;">
                <tr>
                    <td style="font-size:12px; font-weight:bold; color:#17365D; padding:5px 10px; border-bottom:1px solid #000; width:70%;">
                        Account Name: <span style="color:black;">' . htmlspecialchars($all_payments_by_party[0]['ac_name']) . '</span>
                    </td>
                    <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; padding:5px 10px; border-bottom:1px solid #000;border-left:1px solid #000; width:30%;">
                        Print Date: <span style="color:black;">' . htmlspecialchars($currentDate) . '</span>
                    </td>
                </tr>
                <tr>
                    <td style="font-size:12px; font-weight:bold; color:#17365D; padding:5px 10px; border-bottom:1px solid #000; width:70%;">
                    Remarks: <span style="color:black;">' . htmlspecialchars($all_payments_by_party[0]['ac_remarks']) . '</span>
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
                        <th style="width:14%;color:#17365D;font-weight:bold;">Voucher</th>
                        <th style="width:16%;color:#17365D;font-weight:bold;">Account Name</th>
                        <th style="width:21%;color:#17365D;font-weight:bold;">Remarks</th>
                        <th style="width:14%;color:#17365D;font-weight:bold;">Debit</th>
                        <th style="width:14%;color:#17365D;font-weight:bold;">Credit</th>
                    </tr>';
                // Table Rows
            $count = 1;
            $totalAmount = 0;
            $totalAmount2 = 0;
            foreach ($all_payments_by_party as $items) {
                $bgColor = ($count % 2 == 0) ? '#f1f1f1' : '#ffffff';
                $html .= '<tr style="background-color:' . $bgColor . ';">
                                <td>' . $count . '</td>
                                <td>' . Carbon::createFromFormat('Y-m-d', $items['jv_date'])->format('d-m-y') . '</td>
                                <td>' . $items['entry_of'] . '-' . $items['auto_lager'] . '</td>
                                <td>' . $items['ac2'] . '</td>
                                <td>' . $items['Narration'] . '</td>
                                <td>' . number_format($items['Debit'], 0) . '</td>
                                <td>' . number_format($items['Credit'], 0) . '</td>
                            </tr>';

                    $totalAmount += $items['Debit'];
                    $totalAmount2 += $items['Credit'];
                    $count++;
                    }
            // Add totals row
            $html .= '
            <tr style="background-color:#d9edf7; font-weight:bold;">
                <td colspan="5" style="text-align:right;">Total:</td>
                <td style="width:14%;">' . number_format($totalAmount, 0) . '</td>
                <td style="width:14%;">' . number_format($totalAmount2, 0) . '</td>
            </tr>';
                
            $html .= '</table>';
            $pdf->writeHTML($html, true, false, true, false, '');

    
        
    
            // Filename and Output
        $filename = "jv_all_report_{$all_payments_by_party[0]['ac_name']}_from_{$formattedFromDate}_to_{$formattedToDate}.pdf";
        $pdf->Output($filename, 'D');
    }
}
