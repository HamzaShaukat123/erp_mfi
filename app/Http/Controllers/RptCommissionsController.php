<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AC;
use App\Models\comm_pipe_rpt;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CommissionExport;
use App\Services\myPDF;
use Carbon\Carbon;
use Illuminate\Validation\Validator;
use App\Exports\TStockInExport;

class RptCommissionsController extends Controller
{

    public function comm(Request $request){
        $comm_pipe_rpt = comm_pipe_rpt::where('item',$request->acc_id)
        ->whereBetween('sa_date', [$request->fromDate, $request->toDate])
        ->orderBy('ac_name', 'asc')
        ->orderBy('sa_date', 'asc')
        ->get();

        return $comm_pipe_rpt;
    }


    public function commReport(Request $request)
    {
        // Validate the request
        $request->validate([
            'fromDate' => 'required|date',
            'toDate' => 'required|date',
            'outputType' => 'required|in:download,view,excel', // Add 'excel' option
        ]);

        $comm_pipe_rpt = comm_pipe_rpt::where('item', $request->acc_id)
            ->join('item_group', 'comm_pipe_rpt.item', '=', 'item_group.item_group_cod')
            ->whereBetween('sa_date', [$request->fromDate, $request->toDate])
            ->select('comm_pipe_rpt.*', 'item_group.group_name', 'item_group.group_remarks')
            ->orderBy('ac_name', 'asc')
            ->orderBy('sa_date', 'asc')
            ->get();

        // Check if data exists
        if ($comm_pipe_rpt->isEmpty()) {
            return response()->json(['message' => 'No records found for the selected date range.'], 404);
        }

        // Generate PDF if required
        if ($request->outputType === 'view' || $request->outputType === 'download') {
            return $this->commgeneratePDF($comm_pipe_rpt, $request);
        }

        // Export to Excel if requested
        if ($request->outputType === 'excel') {
            $excelData = $this->prepareExcelData($comm_pipe_rpt); // Prepare data for export
            return Excel::download(new CommissionExport($excelData), 'commission_report.xlsx'); // Export as Excel
        }
    }

    private function prepareExcelData($comm_pipe_rpt)
    {
        $data = [];
        $lastAccountName = '';
        $subtotalBAmount = 0;
        $subtotalCommDisc = 0;
        $subtotalCdDisc = 0;

        // Prepare the header row
        $data[] = ['S/No', 'Date', 'Inv #', 'Ord #', 'B-Amount', 'GST / I-Tax', 'Comm %', 'Comm Amt', 'C.d %', 'C.d Amt'];

        foreach ($comm_pipe_rpt as $index => $dataRow) {
            $bAmount = $dataRow['B_amount'] ?? 0;
            $commDisc = ($bAmount * ($dataRow['comm_disc'] ?? 0)) / 100;
            $totalTax = 1 + (((($dataRow['gst'] ?? 0) + ($dataRow['income_tax'] ?? 0)) / 100));
            $cdDisc = ($bAmount && $totalTax !== 0) 
                ? ($bAmount * $totalTax * ($dataRow['cd_disc'] ?? 0) / 100) / $totalTax 
                : 0;

            // Add a row for each entry
            $data[] = [
                $index + 1,
                \Carbon\Carbon::parse($dataRow['sa_date'])->format('d-m-y'),
                $dataRow['Sale_inv_no'] ?? '',
                $dataRow['pur_ord_no'] ?? '',
                number_format($bAmount, 0),
                (($dataRow['gst'] ?? '') . ($dataRow['gst'] && $dataRow['income_tax'] ? " / " : "") . ($dataRow['income_tax'] ?? '')),
                $dataRow['comm_disc'] ?? '',
                number_format($commDisc, 0),
                $dataRow['cd_disc'] ?? '',
                number_format($cdDisc, 0),
            ];

            // Accumulate subtotals
            $subtotalBAmount += $bAmount;
            $subtotalCommDisc += $commDisc;
            $subtotalCdDisc += $cdDisc;

            // Add subtotal row for each account change
            if ($dataRow['ac_name'] !== $lastAccountName) {
                if ($lastAccountName) {
                    $data[] = [
                        '', '', '', 'Subtotal for ' . $lastAccountName, 
                        number_format($subtotalBAmount, 0), '', '', 
                        number_format($subtotalCommDisc, 0), '', number_format($subtotalCdDisc, 0)
                    ];
                    // Reset subtotals
                    $subtotalBAmount = $subtotalCommDisc = $subtotalCdDisc = 0;
                }
                $lastAccountName = $dataRow['ac_name'];
            }
        }

        // Add final subtotal row
        if ($lastAccountName) {
            $data[] = [
                '', '', '', 'Subtotal for ' . $lastAccountName, 
                number_format($subtotalBAmount, 0), '', '', 
                number_format($subtotalCommDisc, 0), '', number_format($subtotalCdDisc, 0)
            ];
        }

        return $data;
    }

        
    // public function commReport(Request $request)
    // {
    //     // Validate the request
    //     $request->validate([
    //         'fromDate' => 'required|date',
    //         'toDate' => 'required|date',
    //         'outputType' => 'required|in:download,view',
    //     ]);
    
    //     $comm_pipe_rpt = comm_pipe_rpt::where('item', $request->acc_id)
    //     ->join('item_group', 'comm_pipe_rpt.item', '=', 'item_group.item_group_cod')
    //     ->whereBetween('sa_date', [$request->fromDate, $request->toDate])
    //     ->select('comm_pipe_rpt.*', 'item_group.group_name', 'item_group.group_remarks')
    //     ->orderBy('ac_name', 'asc')
    //     ->orderBy('sa_date', 'asc')
    //     ->get();

    
    //     // Check if data exists
    //     if ($comm_pipe_rpt->isEmpty()) {
    //         return response()->json(['message' => 'No records found for the selected date range.'], 404);
    //     }
    
    //     // Generate the PDF
    //     return $this->commgeneratePDF($comm_pipe_rpt, $request);
    // }

    private function commgeneratePDF($comm_pipe_rpt, Request $request)
    {
        $currentDate = Carbon::now();
        $formattedDate = $currentDate->format('d-m-y');
        $formattedFromDate = Carbon::parse($request->fromDate)->format('d-m-y');
        $formattedToDate = Carbon::parse($request->toDate)->format('d-m-y');
    
        $pdf = new MyPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('MFI');
        $pdf->SetTitle('Commission Report Of Item ' . $comm_pipe_rpt[0]['group_name']);
        $pdf->SetSubject('Commission Report');
        $pdf->SetKeywords('Commission Report, TCPDF, PDF');
        $pdf->setPageOrientation('P');
    
        // Add a page and set padding
        $pdf->AddPage();
        $pdf->setCellPadding(1.2);
    
        // Report heading
        $heading = '<h1 style="font-size:20px;text-align:center; font-style:italic;text-decoration:underline;color:#17365D">Commission Report</h1>';
        $pdf->writeHTML($heading, true, false, true, false, '');
    
        // Header details
        $html = '
        <table style="border:1px solid #000; width:100%; padding:6px; border-collapse:collapse;">
            <tr>
                 <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; border-bottom:1px solid #000;border-left:1px solid #000; width:70%;">
                    Item Group: <span style="color:black;">' . $comm_pipe_rpt[0]['group_name'] . '</span>
                </td>
                <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; border-bottom:1px solid #000;border-left:1px solid #000; width:30%;">
                    Print Date: <span style="color:black;">' . $formattedDate . '</span>
                </td>
            </tr>
            <tr>
                <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; border-bottom:1px solid #000;border-left:1px solid #000; width:70%;">
                    Group Remarks: <span style="color:black;">' . $comm_pipe_rpt[0]['group_remarks'] . '</span>
                </td>
                <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; border-bottom:1px solid #000;border-left:1px solid #000; width:30%;">
                    From Date: <span style="color:black;">' . $formattedFromDate . '</span>
                </td>
            </tr>
            <tr>
             <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; border-bottom:1px solid #000;border-left:1px solid #000; width:70%;">
                </td>
                <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left;border-left:1px solid #000; width:30%;">
                    To Date: <span style="color:black;">' . $formattedToDate . '</span>
                </td>
            </tr>
        </table>';

        $pdf->writeHTML($html, true, false, true, false, '');

    
        $lastAccountName = '';
        $subtotalBAmount = 0;
        $subtotalCommDisc = 0;
        $subtotalCdDisc = 0;
        $rowNumber = 1;
        $count = 1;

        $html = '
        <table border="1" style="border-collapse: collapse; text-align: center;">
            <tr>
                <th style="width:7%;color:#17365D;font-weight:bold;">S/No</th>
                <th style="width:11%;color:#17365D;font-weight:bold;">Date</th>
                <th style="width:8%;color:#17365D;font-weight:bold;">Inv #</th>
                <th style="width:8%;color:#17365D;font-weight:bold;">Ord #</th>
                <th style="width:12%;color:#17365D;font-weight:bold;">B-Amount</th>
                <th style="width:12%;color:#17365D;font-weight:bold;">GST / I-Tax</th>
                <th style="width:9%;color:#17365D;font-weight:bold;">Comm %</th>
                <th style="width:12%;color:#17365D;font-weight:bold;">Comm Amt</th>
                <th style="width:10%;color:#17365D;font-weight:bold;">C.d %</th>
                <th style="width:12%;color:#17365D;font-weight:bold;">C.d Amt</th>
            </tr>';

            foreach ($comm_pipe_rpt as $data) {
                $bAmount = $data['B_amount'] ?? 0;
                $commDisc = ($bAmount * ($data['comm_disc'] ?? 0)) / 100;
                $totalTax = 1 + (((($data['gst'] ?? 0) + ($data['income_tax'] ?? 0)) / 100));
                $cdDisc = ($bAmount && $totalTax !== 0) 
                    ? ($bAmount * $totalTax * ($data['cd_disc'] ?? 0) / 100) / $totalTax 
                    : 0;

                if ($data['ac_name'] !== $lastAccountName) {
                    if ($lastAccountName) {
                        // Ensure colspan covers the correct number of columns
                        $html .= '
                            <tr style="background-color: #FFFFFF;">
                                <td colspan="4" class="text-center"><strong> Subtotal for ' . $lastAccountName .' </strong></td>
                                <td class="text-danger">' . number_format($subtotalBAmount, 0) . '</td>
                                <td></td>
                                <td></td>
                                <td class="text-danger">' . number_format($subtotalCommDisc, 0) . '</td>
                                <td></td>
                                <td class="text-danger">' . number_format($subtotalCdDisc, 0) . '</td>
                            </tr>';
                        // Reset subtotals
                        $subtotalBAmount = $subtotalCommDisc = $subtotalCdDisc = 0;
                    }

                    // Add account header with correct colspan (10 columns in total)
                    $html .= '
                        <tr>
                            <td colspan="10" style="background-color: #cfe8e3; text-align: center; font-weight: bold;">
                                ' . ($data['ac_name'] ?? "No Account Name") . '
                            </td>
                        </tr>';
                    $lastAccountName = $data['ac_name'];
                    $rowNumber = 1;
                }

                // Add current row
                $html .= '
                    <tr>
                        <td>' . $count++ . '</td>
                        <td>' . ($data['sa_date'] ? \Carbon\Carbon::parse($data['sa_date'])->format('d-m-y') : "") . '</td>
                        <td>' . ($data['Sale_inv_no'] ?? "") . '</td>
                        <td>' . ($data['pur_ord_no'] ?? "") . '</td>
                        <td>' . number_format($bAmount, 0) . '</td>
                        <td>' . (($data['gst'] ?? "") . ($data['gst'] && $data['income_tax'] ? " / " : "") . ($data['income_tax'] ?? "")) . '</td>
                        <td>' . ($data['comm_disc'] ?? "") . '</td>
                        <td>' . number_format($commDisc, 0) . '</td>
                        <td>' . ($data['cd_disc'] ?? "") . '</td>
                        <td>' . number_format($cdDisc, 0) . '</td>
                    </tr>';

                // Accumulate subtotals
                $subtotalBAmount += $bAmount;
                $subtotalCommDisc += $commDisc;
                $subtotalCdDisc += $cdDisc;
            }

            // Final subtotal for the last account
            if ($lastAccountName) {
                $html .= '
                <tr style="background-color: #FFFFFF;">
                    <td colspan="4" class="text-center"><strong>Subtotal for '. $lastAccountName . '</strong></td>
                    <td style="color:red;"><strong>' . number_format($subtotalBAmount, 0) . '</strong></td>
                    <td></td>
                    <td></td>
                    <td style="color:red;"><strong>' . number_format($subtotalCommDisc, 0) . '</strong></td>
                    <td></td>
                    <td style="color:red;"><strong>' . number_format($subtotalCdDisc, 0) . '</strong></td>

                </tr>';
            }

        $html .= '</table>';

        $pdf->writeHTML($html, true, false, true, false, '');

        // // Prepare filename for the PDF
        $fromDate = Carbon::parse($request->fromDate)->format('Y-m-d');
        $toDate = Carbon::parse($request->toDate)->format('Y-m-d');
        $acc_id=$request->acc_id;

        $filename = "commission_report_of_{$comm_pipe_rpt[0]['group_name']}_{$fromDate}_to_{$toDate}.pdf";

        // Determine output type
        if ($request->outputType === 'download') {
            $pdf->Output($filename, 'D'); // For download
        } else {
            $pdf->Output($filename, 'I'); // For inline view
        }
    }

}
