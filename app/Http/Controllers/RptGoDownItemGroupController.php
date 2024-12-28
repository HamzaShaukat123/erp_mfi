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
    
        // Retrieve data from the database
        // $pipe_stock_all_by_item_group = pipe_stock_all_by_item_group::where('item_group_cod',$request->acc_id)
        // ->get();

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
                <td colspan="3" style="text-align:right;">Total:</td>
                <td>' . $totalQty . '</td>
                <td>' . $totalWeight . '</td>
            </tr>';

        $html .= '</table>';
        $pdf->writeHTML($html, true, false, true, false, '');
    

        $filename = "stock_in_report_{$pipe_stock_all_by_item_group[0]['group_name']}.pdf";

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
        $pdf->SetTitle('Stock Out Report Item Group - ' . $request->acc_id);
        $pdf->SetSubject('Stock Out Report');
        $pdf->SetKeywords('Stock Out Report, TCPDF, PDF');
        $pdf->setPageOrientation('P');
    
        // Add a page and set padding
        $pdf->AddPage();
        $pdf->setCellPadding(1.2);
    
        // Report heading
        $heading = '<h1 style="font-size:20px;text-align:center; font-style:italic;text-decoration:underline;color:#17365D">Stock Out Report</h1>';
        $pdf->writeHTML($heading, true, false, true, false, '');
    

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
        $totalAmount = 0;
    
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
            
            $count++;
        }
    
        $html .= '</table>';
        $pdf->writeHTML($html, true, false, true, false, '');
    

        $filename = "stock_out_report.pdf";

        // Determine output type
        if ($request->outputType === 'download') {
            $pdf->Output($filename, 'D'); // For download
        } else {
            $pdf->Output($filename, 'I'); // For inline view
        }
    }

    public function stockAllT(Request $request){
        $pipe_stock_all_by_item_group = pipe_stock_all_by_item_group::where('item_group_cod',$request->acc_id)
        ->get();

        return $pipe_stock_all_by_item_group;
    }

    public function stockAllTReport(Request $request)
    {
        // Validate the request
        $request->validate([
            'acc_id' => 'required',
            'outputType' => 'required|in:download,view',
        ]);
    
        // Retrieve data from the database
        $pipe_stock_all_by_item_group = pipe_stock_all_by_item_group::where('item_group_cod',$request->acc_id)
        ->get();
    
        // Check if data exists
        if ($pipe_stock_all_by_item_group->isEmpty()) {
            return response()->json(['message' => 'No records found for the selected date range.'], 404);
        }
    
        // Generate the PDF
        return $this->stockAllTgeneratePDF($pipe_stock_all_by_item_group, $request);
    }

    private function stockAllTgeneratePDF($pipe_stock_all_by_item_group, Request $request)
    {
        $currentDate = Carbon::now();
        $formattedDate = $currentDate->format('d-m-y');
    
        $pdf = new MyPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('MFI');
        $pdf->SetTitle('Stock All Tabular ' . $request->acc_id);
        $pdf->SetSubject('Stock All Tabular');
        $pdf->SetKeywords('Stock All Tabular, TCPDF, PDF');
        $pdf->setPageOrientation('L');
    
        // Add a page and set padding
        $pdf->AddPage();
        $pdf->setCellPadding(1.2);
    
        // Report heading
        $heading = '<h1 style="font-size:20px;text-align:center; font-style:italic;text-decoration:underline;color:#17365D">Stock All Tabular</h1>';
        $pdf->writeHTML($heading, true, false, true, false, '');
    

        // Table header for data
        $html = '
            <table border="1" style="border-collapse: collapse; text-align: center;">
                <tr>
                    <th style="width:12%;color:#17365D;font-weight:bold;">Item Name</th>
                    <th style="width:8%;color:#17365D;font-weight:bold;">12G/2.50mm</th>
                    <th style="width:8%;color:#17365D;font-weight:bold;">14G/2.00mm</th>
                    <th style="width:8%;color:#17365D;font-weight:bold;">16G/1.60mm</th>
                    <th style="width:8%;color:#17365D;font-weight:bold;">1.50mm</th>
                    <th style="width:8%;color:#17365D;font-weight:bold;">18G/1.20mm</th>
                    <th style="width:8%;color:#17365D;font-weight:bold;">1.10mm</th>
                    <th style="width:8%;color:#17365D;font-weight:bold;">19G/1.0mm</th>
                    <th style="width:8%;color:#17365D;font-weight:bold;">20G/0.9mm</th>
                    <th style="width:8%;color:#17365D;font-weight:bold;">21G/0.8mm</th>
                    <th style="width:8%;color:#17365D;font-weight:bold;">22G/0.7mm</th>
                    <th style="width:8%;color:#17365D;font-weight:bold;">23G/0.6mm</th>
                    <th style="width:8%;color:#17365D;font-weight:bold;">24G/0.5mm</th>
                </tr>';
    
        // Iterate through items and add rows
        $count = 1;
        $totalAmount = 0;
    
        // foreach ($activite11_sales_pipe as $item) {
        //     $backgroundColor = ($count % 2 == 0) ? '#f1f1f1' : '#ffffff'; // Alternating row colors
    
        //     $html .= '
        //         <tr style="background-color:' . $backgroundColor . ';">
        //             <td style="width:7%;">' . $count . '</td>
        //             <td style="width:10%;">' . Carbon::parse($item['sa_date'])->format('d-m-y') . '</td>
        //             <td style="width:10%;">' . $item['Sal_inv_no']. '</td>
        //             <td style="width:10%;">' . $item['pur_ord_no'] . '</td>
        //             <td style="width:22%;">' . $item['acc_name'] . '</td>
        //             <td style="width:15%;">' . $item['comp_name'] . '</td>
        //             <td style="width:15%;">' . $item['Sales_Remarks'] . '</td>
        //             <td style="width:12%;">' . $item['bill_amt'] . '</td>
        //         </tr>';
            
        //     $totalAmount += $item['bill_amt']; // Accumulate total quantity
        //     $count++;
        // }
    
        $html .= '</table>';
        $pdf->writeHTML($html, true, false, true, false, '');
    

        $filename = "stock_all_tabular.pdf";

        // Determine output type
        if ($request->outputType === 'download') {
            $pdf->Output($filename, 'D'); // For download
        } else {
            $pdf->Output($filename, 'I'); // For inline view
        }
    }

}
