<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AC;
use App\Models\sale_by_account;
use App\Exports\Sale1Export;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\myPDF;
use Carbon\Carbon;

class RptAccNameSale1Controller extends Controller
{
    
    public function sale1(Request $request){
        $sale_by_account = sale_by_account::where('ac1',$request->acc_id)
        ->whereBetween('date', [$request->fromDate, $request->toDate])
        ->orderBy('date', 'asc')
        ->get();

        return $sale_by_account;
    }

    public function sale1Excel(Request $request)
    {
        $sale_by_account = sale_by_account::where('ac1', $request->acc_id)
            ->whereBetween('date', [$request->fromDate, $request->toDate])
            ->get();

        $accId = $request->acc_id;
        $fromDate = \Carbon\Carbon::parse($request->fromDate)->format('Y-m-d');
        $toDate = \Carbon\Carbon::parse($request->toDate)->format('Y-m-d');
        
        // Construct the filename
        $filename = "sale1_report_{$accId}_from_{$fromDate}_to_{$toDate}.xlsx";

        // Return the download response with the dynamic filename
        return Excel::download(new Sale1Export($sale_by_account), $filename);
    }

    public function sale1PDF(Request $request)
    {
        // Fetch data for the report
        $sale_by_account = sale_by_account::where('ac1', $request->acc_id)
            ->whereBetween('date', [$request->fromDate, $request->toDate])
            ->leftjoin('ac', 'ac.ac_code', '=', 'sale_by_account.ac1')
            ->select('sale_by_account.*', 'ac.ac_name',  'ac.remarks')
            ->get();

        // Get and format current and report dates
        $currentDate = Carbon::now()->format('d-m-y');
        $formattedFromDate = Carbon::createFromFormat('Y-m-d', $request->fromDate)->format('d-m-y');
        $formattedToDate = Carbon::createFromFormat('Y-m-d', $request->toDate)->format('d-m-y');

        // Initialize PDF
        $pdf = new MyPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('MFI');
        $pdf->SetTitle("Sale 1 Report Of Account - {$sale_by_account[0]['ac_name']}");
        $pdf->SetSubject("Sale 1 Report Of Account - {$sale_by_account[0]['ac_name']}");
        $pdf->SetKeywords('Sale 1 Report, TCPDF, PDF');
        $pdf->setPageOrientation('P');
        $pdf->AddPage();
        $pdf->setCellPadding(1.2);

        // Document header
        $heading = '<h1 style="font-size:20px;text-align:center;font-style:italic;text-decoration:underline;color:#17365D">Sale 1 Report Of Account</h1>';
        $pdf->writeHTML($heading, true, false, true, false, '');

        // Account Info Table
        $html = '
            <table style="border:1px solid #000; width:100%; padding:6px; border-collapse:collapse;">
                <tr>
                    <td style="font-size:12px; font-weight:bold; color:#17365D; padding:5px 10px; border-bottom:1px solid #000; width:70%;">
                        Account Name: <span style="color:black;">' . htmlspecialchars($sale_by_account[0]['ac_name']) . '</span>
                    </td>
                    <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; padding:5px 10px; border-bottom:1px solid #000;border-left:1px solid #000; width:30%;">
                        Print Date: <span style="color:black;">' . htmlspecialchars($currentDate) . '</span>
                    </td>
                </tr>
                <tr>
                    <td style="font-size:12px; font-weight:bold; color:#17365D; padding:5px 10px; border-bottom:1px solid #000; width:70%;">
                    Remarks: <span style="color:black;">' . htmlspecialchars($sale_by_account[0]['remarks']) . '</span>
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
                        <th style="width:14%;color:#17365D;font-weight:bold;">Sales Date</th>
                        <th style="width:16%;color:#17365D;font-weight:bold;">Inv No.</th>
                        <th style="width:11%;color:#17365D;font-weight:bold;">Bill</th>
                        <th style="width:22%;color:#17365D;font-weight:bold;">Name/Address</th>
                        <th style="width:15%;color:#17365D;font-weight:bold;">Remarks</th>
                        <th style="width:15%;color:#17365D;font-weight:bold;">Amount</th>
                    </tr>';

        // Table Rows
        $count = 1;
        $totalAmount = 0;
        foreach ($sale_by_account as $items) {
            $bgColor = ($count % 2 == 0) ? '#f1f1f1' : '#ffffff';
            $html .= '<tr style="background-color:' . $bgColor . ';">
                        <td>' . $count . '</td>
                        <td>' . Carbon::createFromFormat('Y-m-d', $items['date'])->format('d-m-y') . '</td>
                        <td>' . $items['sal_inv'] . '</td>
                        <td>' . $items['bill'] . '</td>
                        <td>' . $items['ac2'] . '</td>
                        <td>' . $items['remarks'] . '</td>
                        <td>' . number_format($items['cr_amt'], 0) . '</td>
                    </tr>';

                $totalAmount += $items['cr_amt'];
                $count++;
            }
            // Add totals row
            $html .= '
            <tr style="background-color:#d9edf7; font-weight:bold;">
                <td colspan="6" style="text-align:right;">Total:</td>
                <td style="width:15%;">' . number_format($totalAmount, 0) . '</td>
            </tr>';
        $html .= '</table>';
        $pdf->writeHTML($html, true, false, true, false, '');

      

        // Filename and Output
        $filename = "sale1_report_{$sale_by_account[0]['ac_name']}_from_{$formattedFromDate}_to_{$formattedToDate}.pdf";
        $pdf->Output($filename, 'I');
    }


    public function sale1Download(Request $request)
    {
        // Fetch data for the report
        $sale_by_account = sale_by_account::where('ac1', $request->acc_id)
            ->whereBetween('date', [$request->fromDate, $request->toDate])
            ->leftjoin('ac', 'ac.ac_code', '=', 'sale_by_account.ac1')
            ->select('sale_by_account.*', 'ac.ac_name',  'ac.remarks')
            ->get();

        // Get and format current and report dates
        $currentDate = Carbon::now()->format('d-m-y');
        $formattedFromDate = Carbon::createFromFormat('Y-m-d', $request->fromDate)->format('d-m-y');
        $formattedToDate = Carbon::createFromFormat('Y-m-d', $request->toDate)->format('d-m-y');

        // Initialize PDF
        $pdf = new MyPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('MFI');
        $pdf->SetTitle("Sale 1 Report Of Account - {$sale_by_account[0]['ac_name']}");
        $pdf->SetSubject("Sale 1 Report Of Account - {$sale_by_account[0]['ac_name']}");
        $pdf->SetKeywords('Sale 1 Report, TCPDF, PDF');
        $pdf->setPageOrientation('P');
        $pdf->AddPage();
        $pdf->setCellPadding(1.2);

        // Document header
        $heading = '<h1 style="font-size:20px;text-align:center;font-style:italic;text-decoration:underline;color:#17365D">Sale 1 Report Of Account</h1>';
        $pdf->writeHTML($heading, true, false, true, false, '');

        // Account Info Table
        $html = '
            <table style="border:1px solid #000; width:100%; padding:6px; border-collapse:collapse;">
                <tr>
                    <td style="font-size:12px; font-weight:bold; color:#17365D; padding:5px 10px; border-bottom:1px solid #000; width:70%;">
                        Account Name: <span style="color:black;">' . htmlspecialchars($sale_by_account[0]['ac_name']) . '</span>
                    </td>
                    <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; padding:5px 10px; border-bottom:1px solid #000;border-left:1px solid #000; width:30%;">
                        Print Date: <span style="color:black;">' . htmlspecialchars($currentDate) . '</span>
                    </td>
                </tr>
                <tr>
                    <td style="font-size:12px; font-weight:bold; color:#17365D; padding:5px 10px; border-bottom:1px solid #000; width:70%;">
                    Remarks: <span style="color:black;">' . htmlspecialchars($sale_by_account[0]['remarks']) . '</span>
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
                        <th style="width:14%;color:#17365D;font-weight:bold;">Sales Date</th>
                        <th style="width:16%;color:#17365D;font-weight:bold;">Inv No.</th>
                        <th style="width:11%;color:#17365D;font-weight:bold;">Bill</th>
                        <th style="width:22%;color:#17365D;font-weight:bold;">Name/Address</th>
                        <th style="width:15%;color:#17365D;font-weight:bold;">Remarks</th>
                        <th style="width:15%;color:#17365D;font-weight:bold;">Amount</th>
                    </tr>';

        // Table Rows
        $count = 1;
        $totalAmount = 0;
        foreach ($sale_by_account as $items) {
            $bgColor = ($count % 2 == 0) ? '#f1f1f1' : '#ffffff';
            $html .= '<tr style="background-color:' . $bgColor . ';">
                        <td>' . $count . '</td>
                        <td>' . Carbon::createFromFormat('Y-m-d', $items['date'])->format('d-m-y') . '</td>
                        <td>' . $items['sal_inv'] . '</td>
                        <td>' . $items['bill'] . '</td>
                        <td>' . $items['ac2'] . '</td>
                        <td>' . $items['remarks'] . '</td>
                        <td>' . number_format($items['cr_amt'], 0) . '</td>
                    </tr>';

                $totalAmount += $items['cr_amt'];
                $count++;
            }
            // Add totals row
            $html .= '
            <tr style="background-color:#d9edf7; font-weight:bold;">
                <td colspan="6" style="text-align:right;">Total:</td>
                <td style="width:15%;">' . number_format($totalAmount, 0) . '</td>
            </tr>';
        $html .= '</table>';
        $pdf->writeHTML($html, true, false, true, false, '');

      

        // Filename and Output
        $filename = "sale1_report_{$sale_by_account[0]['ac_name']}_from_{$formattedFromDate}_to_{$formattedToDate}.pdf";
        $pdf->Output($filename, 'D');
    }
}
