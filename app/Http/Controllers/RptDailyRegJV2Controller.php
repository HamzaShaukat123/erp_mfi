<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AC;
use App\Models\activites9_gen_acas;
use App\Exports\DailyRegJV2Export;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\myPDF;
use Carbon\Carbon;

class RptDailyRegJV2Controller extends Controller
{
    public function jv2(Request $request){
        $activites9_gen_acas = activites9_gen_acas::whereBetween('jv_date', [$request->fromDate, $request->toDate])
        ->orderBy('jv_date')
        ->orderBy('jv_no')
        ->get();

        return $activites9_gen_acas;
    }

    public function jv2Excel(Request $request)
    {
        $activites9_gen_acas = activites9_gen_acas::whereBetween('jv_date', [$request->fromDate, $request->toDate])
        ->get();

        $fromDate = \Carbon\Carbon::parse($request->fromDate)->format('Y-m-d');
        $toDate = \Carbon\Carbon::parse($request->toDate)->format('Y-m-d');
        
        // Construct the filename
        $filename = "daily_reg_jv2_report_from_{$fromDate}_to_{$toDate}.xlsx";

        // Return the download response with the dynamic filename
        return Excel::download(new DailyRegJV2Export($activites9_gen_acas), $filename);
    }

    public function jv2Report(Request $request)
    {
        // Validate the request
        $request->validate([
            'fromDate' => 'required|date',
            'toDate' => 'required|date',
            'outputType' => 'required|in:download,view',
        ]);
    
        // Retrieve data from the database
        $activites9_gen_acas = activites9_gen_acas::whereBetween('jv_date', [$request->fromDate, $request->toDate])
        ->orderBy('jv_date')
        ->orderBy('jv_no')
        ->get();
    
        // Check if data exists
        if ($activites9_gen_acas->isEmpty()) {
            return response()->json(['message' => 'No records found for the selected date range.'], 404);
        }
    
        // Generate the PDF
        return $this->jv2generatePDF($activites9_gen_acas, $request);
    }

    private function jv2generatePDF($activites9_gen_acas, Request $request)
    {
        $currentDate = Carbon::now();
        $formattedDate = $currentDate->format('d-m-y');
        $formattedFromDate = Carbon::parse($request->fromDate)->format('d-m-y');
        $formattedToDate = Carbon::parse($request->toDate)->format('d-m-y');

        $pdf = new MyPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('MFI');
        $pdf->SetTitle('Daily Register JV 2 ' . $request->acc_id);
        $pdf->SetSubject('Daily Register JV 2');
        $pdf->SetKeywords('Daily Register JV 2, TCPDF, PDF');
        $pdf->setPageOrientation('P');

        // Add a page and set padding
        $pdf->AddPage();
        $pdf->setCellPadding(1.2);

        // Report heading
        $heading = '<h1 style="font-size:20px;text-align:center; font-style:italic;text-decoration:underline;color:#17365D">Daily Register JV 2</h1>';
        $pdf->writeHTML($heading, true, false, true, false, '');

        // Header details
        $htmlHeaderDetails = '
        <table style="border:1px solid #000; width:100%; padding:6px; border-collapse:collapse;">
            <tr>
                <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; border-bottom:1px solid #000;border-left:1px solid #000; width:33%;">
                    From Date: <span style="color:black;">' . $formattedFromDate . '</span>
                </td>
                <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left;border-left:1px solid #000; width:33%;">
                    To Date: <span style="color:black;">' . $formattedToDate . '</span>
                </td>
                <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; border-bottom:1px solid #000;border-left:1px solid #000; width:34%;">
                    Print Date: <span style="color:black;">' . $formattedDate . '</span>
                </td>
            </tr>
        </table>';
        $pdf->writeHTML($htmlHeaderDetails, true, false, true, false, '');

        // Table headers with total width = 100%
        $tableHeader = '<tr>
                        <th style="width:10%;color:#17365D;font-weight:bold;text-align:center;">S/No</th>
                        <th style="width:30%;color:#17365D;font-weight:bold;text-align:center;">Account Name</th>
                        <th style="width:30%;color:#17365D;font-weight:bold;text-align:center;">Detail</th>
                        <th style="width:15%;color:#17365D;font-weight:bold;text-align:center;">Debit</th>
                        <th style="width:15%;color:#17365D;font-weight:bold;text-align:center;">Credit</th>
                    </tr>';

        $html = '<table border="1" style="border-collapse: collapse;text-align:center">';
        $html .= $tableHeader;

        $totalDebit = 0;
        $totalCredit = 0;

        // Group the data by `prefix + jv_no`
        $groupedData = [];
        foreach ($activites9_gen_acas as $item) {
            $groupKey = $item['prefix'] . $item['jv_no'];
            if (!isset($groupedData[$groupKey])) {
                $groupedData[$groupKey] = [
                    'header' => [
                        'jv_identifier' => $item['prefix'] . $item['jv_no'],
                        'jv_date' => $item['jv_date'],
                        'narration' => $item['Narration'],
                    ],
                    'rows' => [],
                ];
            }
            $groupedData[$groupKey]['rows'][] = $item;
        }

        // Process grouped data
        foreach ($groupedData as $group) {
            $count = 1;  // Reset count for each group
            $groupTotalDebit = 0;
            $groupTotalCredit = 0;
            
            // Group header
            $html .= '<tr style="background-color:#d2edc7;">
                        <td colspan="5" style="text-align:left;font-weight:bold;">
                            <strong></strong> ' . $group['header']['jv_identifier'] . 
                            ' - <strong>Date:</strong> ' . Carbon::createFromFormat('Y-m-d', $group['header']['jv_date'])->format('d-m-y') . 
                            ' - <strong>Narration:</strong> ' . $group['header']['narration'] . '</td>
                    </tr>';

            // Add rows for the group
            foreach ($group['rows'] as $item) {
                $debit = $item['debit'];
                $credit = $item['credit'];

                $groupTotalDebit += $debit;
                $groupTotalCredit += $credit;

                $totalDebit += $debit;
                $totalCredit += $credit;

                $html .= '<tr>
                            <td>' . $count++ . '</td>
                            <td>' . $item['ac_name'] . '</td>
                            <td>' . $item['Remark'] . '</td>
                            <td>' . number_format($debit, 0) . '</td>
                            <td>' . number_format($credit, 0) . '</td>
                        </tr>';
            }

            // Add group subtotal row
            $html .= '<tr style="background-color:#d9edf7; font-weight:bold;">
                        <td colspan="3" style="text-align:right;">Group Total:</td>
                        <td>' . number_format($groupTotalDebit, 0) . '</td>
                        <td>' . number_format($groupTotalCredit, 0) . '</td>
                    </tr>';
        }

        // Add overall total row
        $html .= '<tr style="background-color:#d9edf7; font-weight:bold;">
                    <td colspan="3" style="text-align:right;">Total:</td>
                    <td>' . number_format($totalDebit, 0) . '</td>
                    <td>' . number_format($totalCredit, 0) . '</td>
                </tr>';

        $html .= '</table>';
        $pdf->writeHTML($html, true, false, true, false, '');

        // Prepare filename for the PDF
        $fromDate = Carbon::parse($request->fromDate)->format('Y-m-d');
        $toDate = Carbon::parse($request->toDate)->format('Y-m-d');

        $filename = "daily_reg_jv2_report_from_{$fromDate}_to_{$toDate}.pdf";

        // Determine output type
        if ($request->outputType === 'download') {
            $pdf->Output($filename, 'D'); // For download
        } else {
            $pdf->Output($filename, 'I'); // For inline view
        }

 }
}
