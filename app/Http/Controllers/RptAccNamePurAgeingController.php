<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\pur_days;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ACNamePURAGEINGExport;
use App\Services\myPDF;
use TCPDF;
use Carbon\Carbon;
use Illuminate\Validation\Validator;

class RptAccNamePurAgeingController extends Controller
{
    public function purAgeing(Request $request){
        $pur_days = pur_days::where('account_name',$request->acc_id)
        ->whereBetween('bill_date', [$request->fromDate, $request->toDate])
        ->orderBy('bill_date','asc')
        ->orderBy('sale_prefix','asc')
        ->get();

        return $pur_days;
    }

    public function purAgeingExcel(Request $request)
    {

        // Fetch the purchase data
        $pur_days = pur_days::where('account_name', $request->acc_id)
        ->whereBetween('bill_date', [$request->fromDate, $request->toDate])
        ->leftjoin('ac', 'ac.ac_code', '=', 'pur_days.account_name')
        ->select('pur_days.*', 'ac.ac_name as ac_nam', 'ac.remarks as ac_remarks')
        ->orderBy('bill_date', 'asc')
        ->orderBy('sale_prefix', 'asc')
        ->get();

        // Prepare data for Excel
        $purchaseData = [];
        $purchaseData[] = [
        'S/No', 'Date', 'Inv No.', 'Detail', 'Bill Amount', 'UnPaid Amount', 'Days', 
        '1-20 Days', '21-35 Days', '36-50 Days', 'Over 50 Days', 'Cleared In Days'
        ];

        $count = 1;
        foreach ($pur_days as $items) {
        $status = $items['remaining_amount'] == 0 ? 'Cleared' : 'Not Cleared';  // Determine the status here
        $maxDaysStyle = $items['remaining_amount'] != 0 ? 'Over 50 Days' : '';  // Handle max days style

        // Calculate the number of days from bill_date to today
        $daysFromBillDate = $items['bill_date'] ? Carbon::parse($items['bill_date'])->diffInDays(Carbon::today()) : '';

        $purchaseData[] = [
            $count,
            Carbon::createFromFormat('Y-m-d', $items['bill_date'])->format('d-m-y'),
            $items["sale_prefix"] . $items["Sal_inv_no"],
            $items["ac2"] . $items["remarks"],
            number_format($items['bill_amount'], 0),
            number_format($items['remaining_amount'], 0),
            $items['remaining_amount'] != 0 ? $daysFromBillDate : '',
            number_format($items['1_20_Days'], 0),
            number_format($items['21_35_Days'], 0),
            number_format($items['36_50_Days'], 0),
            number_format($items['over_50_Days'], 0),
            $items['max_days'] . ' - ' . $status
        ];

        $count++;
        }

        // Filename
        $filename = "Purchase_Ageing_report_{$pur_days[0]['ac_nam']}_from_{$request->fromDate}_to_{$request->toDate}.xlsx";

        // Return Excel download
        return Excel::download(new ACNamePURAGEINGExport($purchaseData), $filename);

    }

    public function purAgeingPDF(Request $request)
    {
       // Query to Fetch Data
$pur_days = pur_days::where('account_name', $request->acc_id)
->whereBetween('bill_date', [$request->fromDate, $request->toDate])
->leftJoin('ac', 'ac.ac_code', '=', 'pur_days.account_name')
->select('pur_days.*', 'ac.ac_name as ac_nam', 'ac.remarks as ac_remarks')
->orderBy('bill_date', 'asc')
->orderBy('sale_prefix', 'asc')
->get();

// Prepare Dates
$currentDate = Carbon::now()->format('d-m-y');
$formattedFromDate = Carbon::createFromFormat('Y-m-d', $request->fromDate)->format('d-m-y');
$formattedToDate = Carbon::createFromFormat('Y-m-d', $request->toDate)->format('d-m-y');

$pdf = new MyPDF();

// Enable automatic page breaks
$pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('MFI');
$pdf->SetTitle("Purchase Ageing Report");
$pdf->SetSubject("Purchase Ageing Report");
$pdf->SetKeywords('Purchase Ageing Report, TCPDF, PDF');
$pdf->setPageOrientation('L');
$pdf->AddPage();
$pdf->setCellPadding(1);

// Header
$heading = '<h1 style="font-size:20px;text-align:center;font-style:italic;text-decoration:underline;color:#17365D">
    Purchase Ageing Report Of Account
</h1>';
$pdf->writeHTML($heading, true, false, true, false, '');

// Account Info Table
$html = '
<table style="border:1px solid #000; width:100%; padding:6px; border-collapse:collapse;">
    <tr>
        <td style="font-size:12px; font-weight:bold; color:#17365D; padding:5px 10px; border-bottom:1px solid #000; width:70%;">
            Account Name: <span style="color:black;">' . htmlspecialchars($pur_days[0]['ac_nam']) . '</span>
        </td>
        <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; padding:5px 10px; border-bottom:1px solid #000;border-left:1px solid #000; width:30%;">
            Print Date: <span style="color:black;">' . htmlspecialchars($currentDate) . '</span>
        </td>
    </tr>
    <tr>
        <td style="font-size:12px; font-weight:bold; color:#17365D; padding:5px 10px; border-bottom:1px solid #000; width:70%;">
            Remarks: <span style="color:black;">' . htmlspecialchars($pur_days[0]['ac_remarks']) . '</span>
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
$html = '
<table border="1" style="border-collapse: collapse; width:100%; text-align:center;">
    <thead>
        <tr>
            <th style="width:4%;color:#17365D; font-weight:bold; border:1px solid #000;">S/No</th>
            <th style="width:9%;color:#17365D; font-weight:bold; border:1px solid #000;">Date</th>
            <th style="width:8%;color:#17365D; font-weight:bold; border:1px solid #000;">Inv No.</th>
            <th style="width:14%;color:#17365D; font-weight:bold; border:1px solid #000;">Detail</th>
            <th style="width:10%;color:#17365D; font-weight:bold; border:1px solid #000;">Bill Amount</th>
            <th style="width:10%;color:#17365D; font-weight:bold; border:1px solid #000;">UnPaid Amount</th>
            <th style="width:5%;color:#17365D; font-weight:bold; border:1px solid #000;">Days</th>
            <th style="width:8%;color:#17365D; font-weight:bold; border:1px solid #000;">1-20 Days</th>
            <th style="width:8%;color:#17365D; font-weight:bold; border:1px solid #000;">21-35 Days</th>
            <th style="width:8%;color:#17365D; font-weight:bold; border:1px solid #000;">36-50 Days</th>
            <th style="width:8%;color:#17365D; font-weight:bold; border:1px solid #000;">Over 50 Days</th>
            <th style="width:8%;color:#17365D; font-weight:bold; border:1px solid #000;">Cleared In Days</th>
        </tr>
    </thead>
    <tbody>';

// Function to check for page breaks
function checkPageBreak($pdf, $rowHeight) {
    if ($pdf->GetY() + $rowHeight > $pdf->getPageHeight() - PDF_MARGIN_BOTTOM) {
        $pdf->AddPage();
    }
}

// Populate rows
$count = 1;
$rowHeight = 10; // Approximate row height
foreach ($pur_days as $items) {
    $bgColor = ($count % 2 == 0) ? '#f1f1f1' : '#ffffff';
    $status = $items['remaining_amount'] == 0 ? 'Cleared' : 'Not Cleared';
    $maxDaysStyle = $items['remaining_amount'] != 0 ? 'style="color:red;"' : '';
    $daysFromBillDate = $items['bill_date'] 
        ? Carbon::parse($items['bill_date'])->diffInDays(Carbon::today()) 
        : '';

    // Check if there's enough space for the row
    checkPageBreak($pdf, $rowHeight);

    // Add the row
    $html .= '<tr style="background-color:' . $bgColor . '; border:1px solid #000;">
    <td style="width:4%; border:1px solid #000;">' . $count . '</td>
    <td style="width:9%; border:1px solid #000;">' . Carbon::createFromFormat('Y-m-d', $items['bill_date'])->format('d-m-y') . '</td>
    <td style="width:8%; border:1px solid #000;">' . htmlspecialchars($items["sale_prefix"] . $items["Sal_inv_no"]) . '</td>
    <td style="width:14%;font-size:9px; border:1px solid #000;">' . $items["ac2"] . $items["remarks"] . '</td>
    <td style="width:10%; border:1px solid #000;">' . number_format($items['bill_amount'], 0) . '</td>
    <td style="width:10%; border:1px solid #000;">' . number_format($items['remaining_amount'], 0) . '</td>
    <td style="width:5%; border:1px solid #000;">' . ($items['remaining_amount'] != 0 ? $daysFromBillDate : '') . '</td>
    <td style="width:8%; border:1px solid #000;">' . number_format($items['1_20_Days'], 0) . '</td>
    <td style="width:8%; border:1px solid #000;">' . number_format($items['21_35_Days'], 0) . '</td>
    <td style="width:8%; border:1px solid #000;">' . number_format($items['36_50_Days'], 0) . '</td>
    <td style="width:8%; border:1px solid #000;">' . number_format($items['over_50_Days'], 0) . '</td>
    <td style="width:8%; border:1px solid #000;" ' . ($items['remaining_amount'] != 0 ? $maxDaysStyle : '') . '>' . 
        ($items['remaining_amount'] == 0 ? $items['max_days'] : '') . 
        ' - ' . $status . 
    '</td>
</tr>';
    $count++;
}

$html .= '</tbody></table>';
$pdf->writeHTML($html, true, false, true, false, '');

// Output the PDF
$filename = "Purchase_Ageing_Report_{$pur_days[0]['ac_nam']}_from_{$formattedFromDate}_to_{$formattedToDate}.pdf";
$pdf->Output($filename, 'I');

    }

    public function purAgeingDownload(Request $request)
    {
        $pur_days = pur_days::where('account_name',$request->acc_id)
        ->whereBetween('bill_date', [$request->fromDate, $request->toDate])
        ->leftjoin('ac', 'ac.ac_code', '=', 'pur_days.account_name')
        ->select('pur_days.*', 'ac.ac_name  as ac_nam', 'ac.remarks as ac_remarks')
        ->orderBy('bill_date','asc')
        ->get();

        
            // Get and format current and report dates
            $currentDate = Carbon::now()->format('d-m-y');
            $formattedFromDate = Carbon::createFromFormat('Y-m-d', $request->fromDate)->format('d-m-y');
            $formattedToDate = Carbon::createFromFormat('Y-m-d', $request->toDate)->format('d-m-y');
    
            // Initialize PDF
            $pdf = new MyPDF();
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('MFI');
            $pdf->SetTitle("Purchase Ageing Report Of Account - {$pur_days[0]['ac_nam']}");
            $pdf->SetSubject("Purchase Ageing Report Of Account - {$pur_days[0]['ac_nam']}");
            $pdf->SetKeywords('Purchase Ageing Report, TCPDF, PDF');
            $pdf->setPageOrientation('L');
            $pdf->AddPage();
            $pdf->setCellPadding(1);
    
            // Document header
            $heading = '<h1 style="font-size:20px;text-align:center;font-style:italic;text-decoration:underline;color:#17365D">Purchase Ageing Report Of Account</h1>';
            $pdf->writeHTML($heading, true, false, true, false, '');
    
            // Account Info Table
            $html = '
                <table style="border:1px solid #000; width:100%; padding:6px; border-collapse:collapse;">
                    <tr>
                        <td style="font-size:12px; font-weight:bold; color:#17365D; padding:5px 10px; border-bottom:1px solid #000; width:70%;">
                            Account Name: <span style="color:black;">' . htmlspecialchars($pur_days[0]['ac_nam']) . '</span>
                        </td>
                        <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; padding:5px 10px; border-bottom:1px solid #000;border-left:1px solid #000; width:30%;">
                            Print Date: <span style="color:black;">' . htmlspecialchars($currentDate) . '</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:12px; font-weight:bold; color:#17365D; padding:5px 10px; border-bottom:1px solid #000; width:70%;">
                        Remarks: <span style="color:black;">' . htmlspecialchars($pur_days[0]['ac_remarks']) . '</span>
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
            $html = '<table border="1" style="border-collapse: collapse; text-align:center; width:100%;">
            <tr>
                <th style="width:4%;color:#17365D; font-weight:bold;">S/No</th>
                <th style="width:9%;color:#17365D; font-weight:bold;">Date</th>
                <th style="width:8%;color:#17365D; font-weight:bold;">Inv No.</th>
                <th style="width:19%; color:#17365D; font-weight:bold;">Detail</th>
                <th style="width:10%;color:#17365D; font-weight:bold;">Bill Amount</th>
                <th style="width:10%;color:#17365D; font-weight:bold;">UnPaid Amount</th>
                <th style="width:8%;color:#17365D; font-weight:bold;">1-20 Days</th>
                <th style="width:8%;color:#17365D; font-weight:bold;">21-35 Days</th>
                <th style="width:8%;color:#17365D; font-weight:bold;">36-50 Days</th>
                <th style="width:8%;color:#17365D; font-weight:bold;">Over 50 Days</th>
                <th style="width:8%;color:#17365D; font-weight:bold;">Cleared In Days</th>
            </tr>';
            // Table Rows
            $count = 1;
            foreach ($pur_days as $items) {
                $bgColor = ($count % 2 == 0) ? '#f1f1f1' : '#ffffff';
                $status = $items['remaining_amount'] == 0 ? 'Cleared' : 'Not Cleared';  // Determine the status here
                $html .= "<tr style='background-color:{$bgColor};'>
                            <td>{$count}</td>
                            <td>" . Carbon::createFromFormat('Y-m-d', $items['bill_date'])->format('d-m-y') . "</td>
                            <td>{$items['sale_prefix']}{$items['Sal_inv_no']}</td>
                            <td>{$items['ac2']}{$items['remarks']}</td>
                            <td>" . number_format($items['bill_amount'], 0) . "</td>
                            <td>" . number_format($items['remaining_amount'], 0) . "</td>
                            <td>" . number_format($items['1_20_Days'], 0) . "</td>
                            <td>" . number_format($items['21_35_Days'], 0) . "</td>
                            <td>" . number_format($items['36_50_Days'], 0) . "</td>
                            <td>" . number_format($items['over_50_Days'], 0) . "</td>
                            <td>{$items['max_days']} - {$status}</td>
                        </tr>";
                $count++;
            }
            $html .= '</table>';
            $pdf->writeHTML($html, true, false, true, false, '');        
    
            // Filename and Output
        $filename = "Sales_Ageing_report_{$pur_days[0]['ac_nam']}_from_{$formattedFromDate}_to_{$formattedToDate}.pdf";
        $pdf->Output($filename, 'D');
    }

}
