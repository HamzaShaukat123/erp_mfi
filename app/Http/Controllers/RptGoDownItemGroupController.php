<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item_entry2;
use App\Models\Item_Groups;
use App\Models\pipe_stock_all_by_item_group;
use App\Models\gd_pipe_pur_by_item_group;
use App\Models\gd_pipe_sales_by_item_group;
use App\Models\AC;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\myPDF;
use Carbon\Carbon;
use Illuminate\Validation\Validator;
use App\Exports\GoDownByItemGrpSIExport;

class RptGoDownItemGroupController extends Controller
{

    public function stockAll(Request $request){
        $pipe_stock_all_by_item_group = pipe_stock_all_by_item_group::where('item_group_cod',$request->acc_id)
        ->where('opp_bal', '<>', 0)
        ->get();

        return $pipe_stock_all_by_item_group;
    }
        
    public function stockAllReport(Request $request)
    {
        // Validate the request
        $request->validate([
            'acc_id' => 'required',
            'outputType' => 'required|in:download,view',
        ]);
    
       

        $pipe_stock_all_by_item_group = pipe_stock_all_by_item_group::where('pipe_stock_all_by_item_group.item_group_cod', $request->acc_id)
        ->where('pipe_stock_all_by_item_group.opp_bal', '<>', 0)
        ->leftJoin('item_group', 'item_group.item_group_cod', '=', 'pipe_stock_all_by_item_group.item_group_cod')
        ->select(
            'pipe_stock_all_by_item_group.item_group_cod',
            'pipe_stock_all_by_item_group.it_cod',
            'pipe_stock_all_by_item_group.item_name',
            'pipe_stock_all_by_item_group.item_remark',
            'pipe_stock_all_by_item_group.opp_bal',
            'pipe_stock_all_by_item_group.wt',
            'item_group.group_name'
        )
        ->get();
    
    
        // Check if data exists
        if ($pipe_stock_all_by_item_group->isEmpty()) {
            return response()->json(['message' => 'No records found for the selected date range.'], 404);
        }
    
        // Generate the PDF
        return $this->stockAllgeneratePDF($pipe_stock_all_by_item_group, $request);
    }

    private function stockAllgeneratePDF($pipe_stock_all_by_item_group, Request $request)
    {
        $currentDate = Carbon::now();
        $formattedDate = $currentDate->format('d-m-y');

        $pdf = new MyPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('MFI');
        $pdf->SetTitle("Stock All Report Item Group  - {$pipe_stock_all_by_item_group[0]['group_name']}");
        $pdf->SetSubject("Stock All Report - {$pipe_stock_all_by_item_group[0]['group_name']}");
        $pdf->SetKeywords('Stock All Report, TCPDF, PDF');
        $pdf->setPageOrientation('P');

        // Add a page and set padding
        $pdf->AddPage();
        $pdf->setCellPadding(1.2);

        // Report heading
        $heading = '<h1 style="font-size:20px;text-align:center; font-style:italic;text-decoration:underline;color:#17365D">Stock All </h1>';
        $pdf->writeHTML($heading, true, false, true, false, '');

        // Header details
        $html = '
        <table style="border:1px solid #000; width:100%; padding:6px; border-collapse:collapse;">
            <tr>
                <td style="font-size:12px; font-weight:bold; color:#17365D; border-bottom:1px solid #000; width:100%;">
                    Item Group: <span style="color:black;">' . $pipe_stock_all_by_item_group[0]['item_group_cod'] . ' - ' . $pipe_stock_all_by_item_group[0]['group_name'] . '</span>
                </td>
            </tr>
        </table>';

        $pdf->writeHTML($html, true, false, true, false, '');

        // Table header for data
        $html = '
            <table border="1" style="border-collapse: collapse; text-align: center;">
                <tr>
                    <th style="width:10%;color:#17365D;font-weight:bold;">S/No.</th>
                    <th style="width:36%;color:#17365D;font-weight:bold;">Item Name</th>
                    <th style="width:24%;color:#17365D;font-weight:bold;">Remarks</th>
                    <th style="width:15%;color:#17365D;font-weight:bold;">Qty. in Hand</th>
                    <th style="width:15%;color:#17365D;font-weight:bold;">Wg. in Hand</th>
                </tr>';

        // Iterate through items and add rows
        $count = 1;
        $totalQty = 0;
        $totalWeight = 0;

        foreach ($pipe_stock_all_by_item_group as $item) {
            $backgroundColor = ($count % 2 == 0) ? '#f1f1f1' : '#ffffff'; // Alternating row colors

            $html .= '
                <tr style="background-color:' . $backgroundColor . ';">
                    <td style="width:10%;">' . $count . '</td>
                    <td style="width:36%;">' . $item['item_name'] . '</td>
                    <td style="width:24%;">' . $item['item_remark'] . '</td>
                    <td style="width:15%;">' . $item['opp_bal'] . '</td>
                    <td style="width:15%;">' . $item['wt'] . '</td>
                </tr>';

            $totalQty += $item['opp_bal'];
            $totalWeight += $item['wt'];
            $count++;
        }

        // Add total row
        $html .= '
            <tr style="font-weight:bold; background-color:#d9edf7;">
                <td colspan="3" style="text-align:right;">Total:</td>
                <td>' . $totalQty . '</td>
                <td>' . $totalWeight . '</td>
            </tr>';

        $html .= '</table>';
        $pdf->writeHTML($html, true, false, true, false, '');

     

        $filename = "stock_all_report_{$pipe_stock_all_by_item_group[0]['group_name']}.pdf";


        // Determine output type
        if ($request->outputType === 'download') {
            $pdf->Output($filename, 'D'); // For download
        } else {
            $pdf->Output($filename, 'I'); // For inline view
        }
    }

    public function stockin(Request $request){

        $gd_pipe_pur_by_item_group = gd_pipe_pur_by_item_group::where('item_group_cod', $request->acc_id)
        ->join('ac', 'ac.ac_code', '=', 'gd_pipe_pur_by_item_group.account_name')
        ->join('item_entry2', 'item_entry2.it_cod', '=', 'gd_pipe_pur_by_item_group.item_cod')
        ->whereBetween('sa_date', [$request->fromDate, $request->toDate])
        ->select('gd_pipe_pur_by_item_group.*', 'ac.ac_name', 'item_entry2.item_name')
        ->get();

        return $gd_pipe_pur_by_item_group;
    }

    public function stockinReport(Request $request)
    {
        // Validate the request
        $request->validate([
            'acc_id' => 'required',
            'outputType' => 'required|in:download,view',
        ]);
    
        $gd_pipe_pur_by_item_group = gd_pipe_pur_by_item_group::where('item_group_cod', $request->acc_id)
        ->join('ac', 'ac.ac_code', '=', 'gd_pipe_pur_by_item_group.account_name')
        ->join('item_entry2', 'item_entry2.it_cod', '=', 'gd_pipe_pur_by_item_group.item_cod')
        ->whereBetween('sa_date', [$request->fromDate, $request->toDate])
        ->select('gd_pipe_pur_by_item_group.*', 'ac.ac_name', 'item_entry2.item_name')
        ->get();
    
        // Check if data exists
        if ($gd_pipe_pur_by_item_group->isEmpty()) {
            return response()->json(['message' => 'No records found for the selected date range.'], 404);
        }
    
        // Generate the PDF
        return $this->stockingeneratePDF($gd_pipe_pur_by_item_group, $request);
    }

    private function stockingeneratePDF($gd_pipe_pur_by_item_group, Request $request)
    {
        $currentDate = Carbon::now();
        $formattedDate = $currentDate->format('d-m-y');
    
        $pdf = new MyPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('MFI');
        $pdf->SetTitle("Stock In Report Item Group  - {$gd_pipe_pur_by_item_group[0]['group_name']}");
        $pdf->SetSubject("Stock In Report - {$gd_pipe_pur_by_item_group[0]['group_name']}");
        $pdf->SetKeywords('Stock In Report, TCPDF, PDF');
        $pdf->setPageOrientation('P');
    
        // Add a page and set padding
        $pdf->AddPage();
        $pdf->setCellPadding(1.2);
    
        // Report heading
        $heading = '<h1 style="font-size:20px;text-align:center; font-style:italic;text-decoration:underline;color:#17365D">Stock In Report</h1>';
        $pdf->writeHTML($heading, true, false, true, false, '');

        // Header details
        $html = '
        <table style="border:1px solid #000; width:100%; padding:6px; border-collapse:collapse;">
            <tr>
                <td style="font-size:12px; font-weight:bold; color:#17365D; border-bottom:1px solid #000; width:100%;">
                    Item Group: <span style="color:black;">' . $gd_pipe_pur_by_item_group[0]['item_group_cod'] . ' - ' . $gd_pipe_pur_by_item_group[0]['group_name'] . '</span>
                </td>
            </tr>
        </table>';

        $pdf->writeHTML($html, true, false, true, false, '');
    

        // Table header for data
        $html = '
            <table border="1" style="border-collapse: collapse; text-align: center;">
                <tr>
                    <th style="width:10%;color:#17365D;font-weight:bold;">S/No.</th>
                    <th style="width:12%;color:#17365D;font-weight:bold;">Voucher</th>
                    <th style="width:25%;color:#17365D;font-weight:bold;">Item Name</th>
                    <th style="width:25%;color:#17365D;font-weight:bold;">Party Name</th>
                    <th style="width:14%;color:#17365D;font-weight:bold;">Quantity</th>
                    <th style="width:14%;color:#17365D;font-weight:bold;">Weight</th>
                </tr>';
    
        // Iterate through items and add rows
        $count = 1;
        $totalQty = 0;
        $totalWeight = 0;
    
        foreach ($gd_pipe_pur_by_item_group as $item) {
            $backgroundColor = ($count % 2 == 0) ? '#f1f1f1' : '#ffffff'; // Alternating row colors
    
            $html .= '
                <tr style="background-color:' . $backgroundColor . ';">
                    <td style="width:10%;">' . $count . '</td>
                    <td style="width:12%;">' . $item['Sal_inv_no'] . '</td>
                    <td style="width:25%;">' . $item['item_name'] . '</td>
                    <td style="width:25%;">' . $item['ac_name'] . '</td>
                    <td style="width:14%;">' . $item['Sales_qty'] . '</td>
                    <td style="width:14%;">' . $item['wt'] . '</td>
                </tr>';

            $totalQty += $item['Sales_qty'];
            $totalWeight += $item['wt'];
            $count++;
        }
    
        // Add total row
        $html .= '
            <tr style="font-weight:bold; background-color:#d9edf7;">
                <td colspan="4" style="text-align:right;">Total:</td>
                <td>' . $totalQty . '</td>
                <td>' . $totalWeight . '</td>
            </tr>';

        $html .= '</table>';
        $pdf->writeHTML($html, true, false, true, false, '');
    

        $filename = "stock_in_report_{$gd_pipe_pur_by_item_group[0]['group_name']}.pdf";

        // Determine output type
        if ($request->outputType === 'download') {
            $pdf->Output($filename, 'D'); // For download
        } else {
            $pdf->Output($filename, 'I'); // For inline view
        }
    }

    public function stockout(Request $request){
        $gd_pipe_sales_by_item_group = gd_pipe_sales_by_item_group::where('item_group_cod', $request->acc_id)
        ->join('ac', 'ac.ac_code', '=', 'gd_pipe_sales_by_item_group.account_name')
        ->join('item_entry2', 'item_entry2.it_cod', '=', 'gd_pipe_sales_by_item_group.item_cod')
        ->whereBetween('sa_date', [$request->fromDate, $request->toDate])
        ->select('gd_pipe_sales_by_item_group.*', 'ac.ac_name', 'item_entry2.item_name')
        ->get();

        return $gd_pipe_sales_by_item_group;
    }

    public function stockoutReport(Request $request)
    {
        // Validate the request
        $request->validate([
            'acc_id' => 'required',
            'outputType' => 'required|in:download,view',
        ]);
    
        $gd_pipe_sales_by_item_group = gd_pipe_sales_by_item_group::where('item_group_cod', $request->acc_id)
        ->join('ac', 'ac.ac_code', '=', 'gd_pipe_sales_by_item_group.account_name')
        ->join('item_entry2', 'item_entry2.it_cod', '=', 'gd_pipe_sales_by_item_group.item_cod')
        ->whereBetween('sa_date', [$request->fromDate, $request->toDate])
        ->select('gd_pipe_sales_by_item_group.*', 'ac.ac_name', 'item_entry2.item_name')
        ->get();
    
        // Check if data exists
        if ($gd_pipe_sales_by_item_group->isEmpty()) {
            return response()->json(['message' => 'No records found for the selected date range.'], 404);
        }
    
        // Generate the PDF
        return $this->stockoutgeneratePDF($gd_pipe_sales_by_item_group, $request);
    }

    private function stockoutgeneratePDF($gd_pipe_sales_by_item_group, Request $request)
    {
        $currentDate = Carbon::now();
        $formattedDate = $currentDate->format('d-m-y');
    
        $pdf = new MyPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('MFI');
        $pdf->SetTitle("Stock Out Report Item Group  - {$gd_pipe_sales_by_item_group[0]['group_name']}");
        $pdf->SetSubject("Stock Out Report - {$gd_pipe_sales_by_item_group[0]['group_name']}");
        $pdf->SetKeywords('Stock Out Report, TCPDF, PDF');
        $pdf->setPageOrientation('P');
    
        // Add a page and set padding
        $pdf->AddPage();
        $pdf->setCellPadding(1.2);
    
        // Report heading
        $heading = '<h1 style="font-size:20px;text-align:center; font-style:italic;text-decoration:underline;color:#17365D">Stock Out Report</h1>';
        $pdf->writeHTML($heading, true, false, true, false, '');


        // Header details
        $html = '
        <table style="border:1px solid #000; width:100%; padding:6px; border-collapse:collapse;">
            <tr>
                <td style="font-size:12px; font-weight:bold; color:#17365D; border-bottom:1px solid #000; width:100%;">
                    Item Group: <span style="color:black;">' . $gd_pipe_sales_by_item_group[0]['item_group_cod'] . ' - ' . $gd_pipe_sales_by_item_group[0]['group_name'] . '</span>
                </td>
            </tr>
        </table>';

        $pdf->writeHTML($html, true, false, true, false, '');
    

        // Table header for data
        $html = '
            <table border="1" style="border-collapse: collapse; text-align: center;">
                <tr>
                    <th style="width:10%;color:#17365D;font-weight:bold;">S/No.</th>
                    <th style="width:12%;color:#17365D;font-weight:bold;">Voucher</th>
                    <th style="width:25%;color:#17365D;font-weight:bold;">Item Name</th>
                    <th style="width:25%;color:#17365D;font-weight:bold;">Party Name</th>
                    <th style="width:14%;color:#17365D;font-weight:bold;">Quantity</th>
                    <th style="width:14%;color:#17365D;font-weight:bold;">Weight</th>
                </tr>';
    
        // Iterate through items and add rows
        $count = 1;
        $totalQty = 0;
        $totalWeight = 0;
    
        foreach ($gd_pipe_sales_by_item_group as $item) {
            $backgroundColor = ($count % 2 == 0) ? '#f1f1f1' : '#ffffff'; // Alternating row colors
    
            $html .= '
                <tr style="background-color:' . $backgroundColor . ';">
                    <td style="width:10%;">' . $count . '</td>
                    <td style="width:12%;">' . $item['Sal_inv_no'] . '</td>
                    <td style="width:25%;">' . $item['item_name'] . '</td>
                    <td style="width:25%;">' . $item['ac_name'] . '</td>
                    <td style="width:14%;">' . $item['Sales_qty'] . '</td>
                    <td style="width:14%;">' . $item['wt'] . '</td>
                </tr>';

            $totalQty += $item['Sales_qty'];
            $totalWeight += $item['wt'];
            $count++;
        }

        // Add total row
        $html .= '
        <tr style="font-weight:bold; background-color:#d9edf7;">
            <td colspan="4" style="text-align:right;">Total:</td>
            <td>' . $totalQty . '</td>
            <td>' . $totalWeight . '</td>
        </tr>';
    
        $html .= '</table>';
        $pdf->writeHTML($html, true, false, true, false, '');
    
        $filename = "stock_out_report_{$gd_pipe_sales_by_item_group[0]['group_name']}.pdf";

        // Determine output type
        if ($request->outputType === 'download') {
            $pdf->Output($filename, 'D'); // For download
        } else {
            $pdf->Output($filename, 'I'); // For inline view
        }
    }

    public function stockAllT(Request $request){
        $pipe_stock_all_by_item_group = pipe_stock_all_by_item_group::where('item_group_cod', $request->acc_id)
        ->get();


        return $pipe_stock_all_by_item_group;
    }

    public function stockAllTabularReport(Request $request)
    {
        // Validate the request
        $request->validate([
            'acc_id' => 'required|exists:pipe_stock_all_by_item_group,item_group_cod', // Ensure acc_id exists
            'outputType' => 'required|in:download,view',
        ]);
    
        // Retrieve data from the database
        $pipe_stock_all_by_item_group = pipe_stock_all_by_item_group::where('pipe_stock_all_by_item_group.item_group_cod', $request->acc_id)
            ->leftJoin('item_group', 'item_group.item_group_cod', '=', 'pipe_stock_all_by_item_group.item_group_cod')
            ->select(
                'pipe_stock_all_by_item_group.item_group_cod',
                'pipe_stock_all_by_item_group.it_cod',
                'pipe_stock_all_by_item_group.item_name',
                'pipe_stock_all_by_item_group.item_remark',
                'pipe_stock_all_by_item_group.opp_bal',
                'pipe_stock_all_by_item_group.wt',
                'item_group.group_name' // Include group_name in the select
            )
            ->get();
    
        // If no records are found
        if ($pipe_stock_all_by_item_group->isEmpty()) {
            return response()->json(['message' => 'No records found.'], 404);
        }
    
        // Extract group_name from the first item of the dataset
        $groupName = $pipe_stock_all_by_item_group->first()->group_name ?? 'Unknown Group';
    
        // Process the data to break the item_name into chunks and group the items
        $processedData = $pipe_stock_all_by_item_group->map(function ($item) {
            $itemChunks = explode(' ', $item->item_name);
            $item_group = $itemChunks[0] ?? '';   // First chunk (before the first space)
            $item_gauge = $itemChunks[1] ?? '';   // Second chunk (between the first and second space)
            $item_name = implode(' ', array_slice($itemChunks, 2)) ?? ''; // Everything after the second space
    
            return [
                'item_group' => $item_group,
                'item_mm' => $item_gauge,
                'item_name' => $item_name,
                'group_name' => $item->group_name, // Keep the group_name in the processed data
                'opp_bal' => $item->opp_bal ?? 0, // Default to 0 if opp_bal is null
            ];
        });
    
        // Separate the items into three groups: ROUND X (start with 'ROUND X'), SQR (end with 'SQR'), and others (neither ROUND X nor SQR)
        $roundItems = $processedData->filter(function ($item) {
            return strpos($item['item_name'], 'ROUND X') === 0; // Check if it starts with 'ROUND X'
        })->sortBy('item_name'); // Sort ROUND X items in ascending order
    
        $sqrItems = $processedData->filter(function ($item) {
            return substr($item['item_name'], -3) === 'SQR'; // Check if it ends with 'SQR'
        })->sortBy('item_name'); // Sort SQR items in ascending order
    
        $otherItems = $processedData->filter(function ($item) {
            return !(strpos($item['item_name'], 'ROUND X') === 0 || substr($item['item_name'], -3) === 'SQR'); // Exclude ROUND X and SQR
        })->sortBy('item_name'); // Sort other items in ascending order
    
        // Merge the groups in the order: ROUND, SQR, others (ensure there is no mixing)
        $orderedData = $roundItems->merge($sqrItems)->merge($otherItems);
    
        // Group the items by item_name (maintains the separate groups in order)
        $groupedByItemName = $orderedData->groupBy('item_name');
    
        // Check if data exists
        if ($groupedByItemName->isEmpty()) {
            return response()->json(['message' => 'No records found for the selected date range.'], 404);
        }
    
        // Determine if this is a 'star' or 'non-star' report based on the route or parameter
        if (strpos($request->path(), 'star') !== false) {
            // For star report, call the star PDF generation method
            return $this->stockAllTabularStargeneratePDF($groupedByItemName, $groupName, $request);
        } else if (strpos($request->path(), 'filtered') !== false) {
            // For filter report, call the filter PDF generation method
            return $this->stockAllTabularFilteredgeneratePDF($groupedByItemName, $groupName, $request);
        } else {
            // For non-star report, call the non-star PDF generation method
            return $this->stockAllTabulargeneratePDF($groupedByItemName, $groupName, $request);
        }

    }
    
    private function stockAllTabulargeneratePDF($groupedByItemName, $groupName, $request)
    {
        $currentDate = Carbon::now();
        $formattedDate = $currentDate->format('d-m-y');
    
        // Initialize PDF (ensure MyPDF or TCPDF is correctly included and loaded)
        $pdf = new MyPDF(); // Replace MyPDF with TCPDF if applicable
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('MFI');
        $pdf->SetTitle("Stock All Report - {$groupName}");
        $pdf->SetSubject("Stock All Report - {$groupName}");
        $pdf->SetKeywords('Stock All Tabular, TCPDF, PDF');
        $pdf->setPageOrientation('L');
    
        // Add a page and set padding
        $pdf->AddPage();
        $pdf->setCellPadding(1.2);
    
        // Dynamic heading
        $headingStyle = "font-size:20px;text-align:center;font-style:italic;text-decoration:underline;color:#17365D;";
        $heading = "<h1 style=\"{$headingStyle}\">Stock All Tabular - {$groupName} (Generated: {$formattedDate})</h1>";
        $pdf->writeHTML($heading, true, false, true, false, '');
    
        // Table header for data
        $html = '<table border="1" style="border-collapse: collapse; text-align: center; width: 100%;">';
        $html .= '<tr>';
        $html .= '<th style="width: 28%;color:#17365D;font-weight:bold;">Item Name</th>';
    
        // Dynamically determine the available gauges
        $allGauges = [];
        foreach ($groupedByItemName as $items) {
            foreach ($items as $item) {
                if (isset($item['item_mm'])) {
                    $allGauges[$item['item_mm']] = true; // Use the gauge as a key for unique values
                }
            }
        }
        $availableGauges = array_keys($allGauges); // Extract unique gauges
    
        // Sort gauges in natural order
        natsort($availableGauges);
        $availableGauges = array_values($availableGauges); // Reindex after sorting
    
        $remainingWidth = 72; // Remaining width for the other columns
        $numColumns = count($availableGauges); // Count dynamically available gauges
    
        // Calculate the width for the remaining columns
        $columnWidth = $numColumns > 0 ? $remainingWidth / $numColumns : 0;
    
        foreach ($availableGauges as $gauge) {
            $html .= "<th style=\"width: {$columnWidth}%;color:#17365D;font-weight:bold;\">{$gauge}</th>";
        }
        $html .= '</tr>';
    
        // Generate table rows
        $count = 0;
        foreach ($groupedByItemName as $itemName => $items) {
            $backgroundColor = ($count % 2 == 0) ? '#f1f1f1' : '#ffffff';
            $count++;
    
            $html .= '<tr style="background-color:' . $backgroundColor . ';">';
            $html .= "<td style=\"font-size: 12px;\">{$itemName}</td>";
    
            foreach ($availableGauges as $gauge) {
                $item = $items->firstWhere('item_mm', $gauge);
                $value = $item ? $item['opp_bal'] : null;
    
                if ($value !== null && $value != 0) {
                    $html .= "<td style=\"text-align: center; font-size: 12px; color: red;\">{$value}</td>";
                } else {
                    $html .= "<td style=\"text-align: center; font-size: 12px;\">{$value}</td>";
                }
            }
    
            $html .= '</tr>';
        }
        $html .= '</table>';
        $pdf->writeHTML($html, true, false, true, false, '');
    
        $filename = "stock_all_tabular_{$groupName}.pdf";
    
        // Determine output type
        if ($request->outputType === 'download') {
            $pdf->Output($filename, 'D'); // For download
        } else {
            $pdf->Output($filename, 'I'); // For inline view
        }
    }
    
                
    public function stockAllTabularStargeneratePDF($groupedByItemName, $groupName, $request)
    {
        $currentDate = Carbon::now();
        $formattedDate = $currentDate->format('d-m-y');

        // Initialize PDF (ensure MyPDF or TCPDF is correctly included and loaded)
        $pdf = new MyPDF(); // Replace MyPDF with TCPDF if applicable
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('MFI');
        $pdf->SetTitle("Star Stock All Report - {$groupName}");
        $pdf->SetSubject("Star Stock All Report - {$groupName}");
        $pdf->SetKeywords('Star Stock All Tabular, TCPDF, PDF');
        $pdf->setPageOrientation('L');

        // Add a page and set padding
        $pdf->AddPage();
        $pdf->setCellPadding(1.2);

        // Dynamic heading
        $headingStyle = "font-size:20px;text-align:center;font-style:italic;text-decoration:underline;color:#17365D;";
        $heading = "<h1 style=\"{$headingStyle}\">Stock All Tabular - {$groupName} (Generated: {$formattedDate})</h1>";
        $pdf->writeHTML($heading, true, false, true, false, '');

        // Table header for data
        $html = '<table border="1" style="border-collapse: collapse; text-align: center; width: 100%;">';
        $html .= '<tr>';
        $html .= '<th style="width: 28%;color:#17365D;font-weight:bold;">Item Name</th>';

        // Dynamically determine the available gauges
        $allGauges = [];
        foreach ($groupedByItemName as $items) {
            foreach ($items as $item) {
                if (isset($item['item_mm'])) {
                    $allGauges[$item['item_mm']] = true; // Use the gauge as a key for unique values
                }
            }
        }
        $availableGauges = array_keys($allGauges); // Extract unique gauges

        // Sort gauges in natural order
        natsort($availableGauges);
        $availableGauges = array_values($availableGauges); // Reindex after sorting

        // Determine which gauges have non-zero, non-null data
        $validGauges = [];
        foreach ($availableGauges as $gauge) {
            $hasValidData = false;
            foreach ($groupedByItemName as $items) {
                $item = $items->firstWhere('item_mm', $gauge);
                if ($item && $item['opp_bal'] > 0) {
                    $hasValidData = true;
                    break;
                }
            }
            if ($hasValidData) {
                $validGauges[] = $gauge; // Add to valid gauges if it has data
            }
        }

        // Remaining columns width calculation
        $remainingWidth = 72;
        $numColumns = count($validGauges);
        $columnWidth = $numColumns > 0 ? $remainingWidth / $numColumns : 0;

        $html .= '<th style="width: ' . $columnWidth . '%;color:#17365D;font-weight:bold;">' . implode('</th><th style="width: ' . $columnWidth . '%;color:#17365D;font-weight:bold;">', $validGauges) . '</th>';
        $html .= '</tr>';

        // Generate table rows
        $count = 0;
        foreach ($groupedByItemName as $itemName => $items) {
            $backgroundColor = ($count % 2 == 0) ? '#f1f1f1' : '#ffffff';
            $count++;

            $htmlRow = "<td style=\"font-size: 12px;\">{$itemName}</td>";
            $hasData = false; // Initialize flag to check if there's any data for the row

            // Only iterate through valid gauges
            foreach ($validGauges as $gauge) {
                $item = $items->firstWhere('item_mm', $gauge);
                $value = $item ? $item['opp_bal'] : null;

                // Only add the gauge column if the value is not null or zero
                if ($value !== null && $value > 0) {
                    $htmlRow .= "<td style=\"text-align: center; font-size: 12px; color: red;\">AV</td>";
                    $hasData = true;
                } else {
                    $htmlRow .= "<td style=\"text-align: center; font-size: 12px;\">x</td>";
                }

                
            }

            // Only add row if it has data
            if ($hasData) {
                $html .= "<tr style=\"background-color: {$backgroundColor};\">{$htmlRow}</tr>";
            }
        }
        $html .= '</table>';
        $pdf->writeHTML($html, true, false, true, false, '');

        $filename = "star_stock_all_tabular_{$groupName}.pdf";

        // Determine output type
        if ($request->outputType === 'download') {
            $pdf->Output($filename, 'D'); // For download
        } else {
            $pdf->Output($filename, 'I'); // For inline view
        }

    }


    public function stockAllTabularFilteredgeneratePDF($groupedByItemName, $groupName, $request)
    {
        $currentDate = Carbon::now();
        $formattedDate = $currentDate->format('d-m-y');

        // Initialize PDF (ensure MyPDF or TCPDF is correctly included and loaded)
        $pdf = new MyPDF(); // Replace MyPDF with TCPDF if applicable
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('MFI');
        $pdf->SetTitle("Filtered Stock All Report - {$groupName}");
        $pdf->SetSubject("Filtered Stock All Report - {$groupName}");
        $pdf->SetKeywords('Filtered Stock All Tabular, TCPDF, PDF');
        $pdf->setPageOrientation('L');

        // Add a page and set padding
        $pdf->AddPage();
        $pdf->setCellPadding(1.2);

        // Dynamic heading
        $headingStyle = "font-size:20px;text-align:center;font-style:italic;text-decoration:underline;color:#17365D;";
        $heading = "<h1 style=\"{$headingStyle}\">Stock All Tabular - {$groupName} (Generated: {$formattedDate})</h1>";
        $pdf->writeHTML($heading, true, false, true, false, '');

        // Table header for data
        $html = '<table border="1" style="border-collapse: collapse; text-align: center; width: 100%;">';
        $html .= '<tr>';
        $html .= '<th style="width: 28%;color:#17365D;font-weight:bold;">Item Name</th>';

        // Dynamically determine the available gauges
        $allGauges = [];
        foreach ($groupedByItemName as $items) {
            foreach ($items as $item) {
                if (isset($item['item_mm'])) {
                    $allGauges[$item['item_mm']] = true; // Use the gauge as a key for unique values
                }
            }
        }
        $availableGauges = array_keys($allGauges); // Extract unique gauges

        // Sort gauges in natural order
        natsort($availableGauges);
        $availableGauges = array_values($availableGauges); // Reindex after sorting

        // Determine which gauges have non-zero, non-null data
        $validGauges = [];
        foreach ($availableGauges as $gauge) {
            $hasValidData = false;
            foreach ($groupedByItemName as $items) {
                $item = $items->firstWhere('item_mm', $gauge);
                if ($item && $item['opp_bal'] > 0) {
                    $hasValidData = true;
                    break;
                }
            }
            if ($hasValidData) {
                $validGauges[] = $gauge; // Add to valid gauges if it has data
            }
        }

        // Remaining columns width calculation
        $remainingWidth = 72;
        $numColumns = count($validGauges);
        $columnWidth = $numColumns > 0 ? $remainingWidth / $numColumns : 0;

        $html .= '<th style="width: ' . $columnWidth . '%;color:#17365D;font-weight:bold;">' . implode('</th><th style="width: ' . $columnWidth . '%;color:#17365D;font-weight:bold;">', $validGauges) . '</th>';
        $html .= '</tr>';

        // Generate table rows
        $count = 0;
        foreach ($groupedByItemName as $itemName => $items) {
            $backgroundColor = ($count % 2 == 0) ? '#f1f1f1' : '#ffffff';
            $count++;

            $htmlRow = "<td style=\"font-size: 12px;\">{$itemName}</td>";
            $hasData = false; // Initialize flag to check if there's any data for the row

            // Only iterate through valid gauges
            foreach ($validGauges as $gauge) {
                $item = $items->firstWhere('item_mm', $gauge);
                $value = $item ? $item['opp_bal'] : null;

                // Only add the gauge column if the value is not null or zero
                if ($value !== null && $value > 0) {
                    $htmlRow .= "<td style=\"text-align: center; font-size: 12px; color: red;\">{$value}</td>";
                    $hasData = true;
                } else {
                    $htmlRow .= "<td style=\"text-align: center; font-size: 12px;\">{$value}</td>";
                }

                
            }

            // Only add row if it has data
            if ($hasData) {
                $html .= "<tr style=\"background-color: {$backgroundColor};\">{$htmlRow}</tr>";
            }
        }
        $html .= '</table>';
        $pdf->writeHTML($html, true, false, true, false, '');

        $filename = "filter_stock_all_tabular_{$groupName}.pdf";

        // Determine output type
        if ($request->outputType === 'download') {
            $pdf->Output($filename, 'D'); // For download
        } else {
            $pdf->Output($filename, 'I'); // For inline view
        }

    }
    
}
