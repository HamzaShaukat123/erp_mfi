<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AC;
use App\Models\pur_account_item_group_info;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\myPDF;
use Carbon\Carbon;

class RptItemGroupPur1Controller extends Controller
{
    public function purchase1(Request $request){
        $pur_account_item_group_info = pur_account_item_group_info::where('item_group_code',$request->acc_id)
        ->whereBetween('pur_date', [$request->fromDate, $request->toDate])
        ->orderBy('pur_date', 'asc')
        ->get(['prefix', 'pur_id','pur_date', 'ac_name', 'weight','qty', 'price', 'item_name']);

        return $pur_account_item_group_info;
    }

    public function ItemGroupPur1Report(Request $request)
    {
        // Validate the request
        $request->validate([
            'fromDate' => 'required|date',
            'toDate' => 'required|date',
            'acc_id' => 'required',
            'outputType' => 'required|in:download,view',
        ]);
        
        $pur_account_item_group_info = pur_account_item_group_info::where('item_group_code',$request->acc_id)
        ->whereBetween('pur_date', [$request->fromDate, $request->toDate])
        ->orderBy('pur_date', 'asc')
        ->get(['prefix', 'pur_id','pur_date', 'ac_name', 'item_group_name','item_name', 'weight','qty', 'price']);
    
        // Check if data exists
        if ($pur_account_item_group_info->isEmpty()) {
            return response()->json(['message' => 'No records found for the selected date range.'], 404);
        }
    
        // Generate the PDF
        return $this->ItemGroupPur1PDF($pur_account_item_group_info, $request);
    }

    private function ItemGroupPur1PDF($pur_account_item_group_info, Request $request)
    {
        $currentDate = Carbon::now();
        $formattedDate = $currentDate->format('d-m-y');
        $formattedFromDate = Carbon::parse($request->fromDate)->format('d-m-y');
        $formattedToDate = Carbon::parse($request->toDate)->format('d-m-y');
    
        $pdf = new MyPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('MFI');
        $pdf->SetTitle('Purchase Report Of Item Group - ' . $pur_account_item_group_info[0]['item_group_name']);
        $pdf->SetSubject('Purchase Report');
        $pdf->SetKeywords('Purchase Report, TCPDF, PDF');
        $pdf->setPageOrientation('P');
    
        // Add a page and set padding
        $pdf->AddPage();
        $pdf->setCellPadding(1.2);
    
        // Report heading
        $heading = '<h1 style="font-size:20px;text-align:center; font-style:italic;text-decoration:underline;color:#17365D">Purchase Report Of Item Group</h1>';
        $pdf->writeHTML($heading, true, false, true, false, '');
    
        // Header details
        $html = '
        <table style="border:1px solid #000; width:100%; padding:6px; border-collapse:collapse;">
            <tr>
                <td style="font-size:12px; font-weight:bold; color:#17365D; border-bottom:1px solid #000; width:70%;">
                    Item Group: <span style="color:black;">' . $pur_account_item_group_info[0]['item_group_name'] . '</span>
                </td>
                <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; border-bottom:1px solid #000;border-left:1px solid #000; width:30%;">
                    Print Date: <span style="color:black;">' . $formattedDate . '</span>
                </td>
            </tr>
            <tr>
                <td></td>
                <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; border-bottom:1px solid #000;border-left:1px solid #000; width:30%;">
                    From Date: <span style="color:black;">' . $formattedFromDate . '</span>
                </td>
            </tr>
            <tr>
                <td></td>
                <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left;border-left:1px solid #000; width:30%;">
                    To Date: <span style="color:black;">' . $formattedToDate . '</span>
                </td>
            </tr>
        </table>';

        $pdf->writeHTML($html, true, false, true, false, '');

    
        // Table header for data
        $html = '
            <table border="1" style="border-collapse: collapse; text-align: center;">
                <tr>
                    <th style="width:7%;color:#17365D;font-weight:bold;">S/No</th>
                    <th style="width:14%;color:#17365D;font-weight:bold;">Date</th>
                    <th style="width:13%;color:#17365D;font-weight:bold;">Inv ID</th>
                    <th style="width:18%;color:#17365D;font-weight:bold;">Account Name</th>
                    <th style="width:13%;color:#17365D;font-weight:bold;">Item Name</th>
                    <th style="width:11%;color:#17365D;font-weight:bold;">Qty</th>
                    <th style="width:12%;color:#17365D;font-weight:bold;">Price</th>
                    <th style="width:12%;color:#17365D;font-weight:bold;">Weight</th>
                </tr>';
        // Initialize total variables
        $totalQty = 0;
        $totalWeight = 0;
        $totalAmount = 0;

        // Iterate through items and add rows
        $count = 1;

        foreach ($pur_account_item_group_info as $item) {
            $backgroundColor = ($count % 2 == 0) ? '#f1f1f1' : '#ffffff'; // Alternating row colors

            // Calculate amount
            $amount = $item['price'] * $item['weight'];

            // Accumulate totals
            $totalQty += $item['qty'];
            $totalWeight += $item['weight'];
            $totalAmount += $amount;

            $html .= '
                <tr style="background-color:' . $backgroundColor . ';">
                    <td style="width:7%;">' . $count . '</td>
                    <td style="width:14%;">' . Carbon::parse($item['pur_date'])->format('d-m-y') . '</td>
                    <td style="width:13%;">' . $item['prefix'] . $item['pur_id'] . '</td>
                    <td style="width:18%;">' . $item['ac_name'] . '</td>
                    <td style="width:13%;">' . $item['item_name'] . '</td>
                    <td style="width:11%;">' . $item['qty'] . '</td>
                    <td style="width:12%;">' . $item['price'] . '</td>
                    <td style="width:12%;">' . $item['weight'] . '</td>
                </tr>';
            
            $count++;
        }

        // Add totals row
        $html .= '
            <tr style="background-color:#d9edf7; font-weight:bold;">
                <td colspan="5" style="text-align:right;">Total</td>
                <td style="width:11%;">' . $totalQty . '</td>
                <td style="width:12%;">--</td>
                <td style="width:12%;">' . $totalWeight . '</td>
            </tr>';

        $html .= '</table>';
        $pdf->writeHTML($html, true, false, true, false, '');

      
    
        // Prepare filename for the PDF
        $accId = $request->acc_id;
        $fromDate = Carbon::parse($request->fromDate)->format('Y-m-d');
        $toDate = Carbon::parse($request->toDate)->format('Y-m-d');
        $filename = "Purchase_item_group_report_{$accId}_from_{$fromDate}_to_{$toDate}.pdf";
    
        // Determine output type
        if ($request->outputType === 'download') {
            $pdf->Output($filename, 'D'); // For download
        } else {
            $pdf->Output($filename, 'I'); // For inline view
        }
    }
}
