<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\balance_all;
use App\Exports\ACGroupBAExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\myPDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RptAccGrpBAController extends Controller
{

    public function ba(Request $request){
        $balance_all = balance_all::all()->groupBy('heads');

        return $balance_all;
    }

    public function baExcel(Request $request)
    {
        $balance_all = balance_all::all()->groupBy('heads');
    
        $filename = "balance_all.xlsx";
    
        return Excel::download(new ACGroupBAExport($balance_all), $filename);
    }
    

    public function baReport(Request $request)
    {
        $balance_all = balance_all::all()->groupBy('heads');
 
        // Check if data exists
        if ($balance_all->isEmpty()) {
            return response()->json(['message' => 'No records found for the Account.'], 404);
        }
    
        // Generate the PDF
        return $this->bageneratePDF($balance_all, $request);
    }

    private function bageneratePDF($balance_all, Request $request){
        $currentDate = Carbon::now();
        $formattedDate = $currentDate->format('d-m-y');

        $pdf = new MyPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('MFI');
        $pdf->SetTitle('Balance All Report');
        $pdf->SetSubject('Balance All Report');
        $pdf->SetKeywords('Balance All Report, TCPDF, PDF');
        $pdf->setPageOrientation('P');  // Page Orientation: Portrait

        // Add a page and set padding
        $pdf->AddPage();
        $pdf->setCellPadding(1.2);

        // Report heading
        $heading = '<h1 style="font-size:20px;text-align:center; font-style:italic;text-decoration:underline;color:#17365D">Balance All - ' . $formattedDate . '</h1>';
        $pdf->writeHTML($heading, true, false, true, false, '');

        // Group the data
        $groupedData = $this->groupByHeadAndSub($balance_all);

        // Initialize HTML content
        $html = '';
        $rowCount = 1;
        $totalDebit = 0;
        $totalCredit = 0;

        // Start the main table and add the header only once
        $html .= '<table border="1" style="border-collapse: collapse; width: 100%; text-align: center;">';

        // Loop through grouped data
        foreach ($groupedData as $headCount => $heads) {
            // Initialize head totals
            $headTotalDebit = 0;
            $headTotalCredit = 0;

            // Add the sub-header for each $headCount
            $html .= '<tr><td colspan="6" style="text-align:center; font-size:18px; font-weight:600; background-color: #d2edc7; border: 1px solid #000; color: red;">
                    <strong>' . $headCount . '</strong>
                    </td></tr>';

            foreach ($heads as $subHeadCount => $subheads) {
                // Reset subtotals for the current subhead
                $subTotalDebit = 0;
                $subTotalCredit = 0;

                // Add sub-header row
                $html .= '<tr><td colspan="6" style="text-align:center; font-size:16px; font-weight:500; background-color: #e2f3f5; border: 1px solid #000;">
                        <strong>' . $subHeadCount . '</strong>
                        </td></tr><tr>
                <th style="width:8%; color:#17365D; font-weight:bold;">S/No</th>
                <th style="width:10%; color:#17365D; font-weight:bold;">AC</th>
                <th style="width:25%; color:#17365D; font-weight:bold;">Account Name</th>
                <th style="width:25%; color:#17365D; font-weight:bold;">Address</th>
                <th style="width:16%; color:#17365D; font-weight:bold;">Debit</th>
                <th style="width:16%; color:#17365D; font-weight:bold;">Credit</th>
                </tr>';

                // Iterate through subhead data rows
                foreach ($subheads as $item) {
                    // Add data row with alternating background colors
                    $backgroundColor = ($rowCount % 2 == 0) ? '#f1f1f1' : '#ffffff';

                    $html .= '<tr style="background-color:' . $backgroundColor . ';">
                            <td>' . $rowCount . '</td>
                            <td>' . $item['ac_code'] . '</td>
                            <td>' . $item['ac_name'] . '</td>
                            <td>' . $item['address'] . ' ' . $item['phone'] . '</td>
                            <td>' . number_format($item['Debit'], 0) . '</td>
                            <td>' . number_format($item['Credit'], 0) . '</td>
                            </tr>';

                    // Update subtotals and totals
                    $subTotalDebit += (float) $item['Debit']; // Cast to ensure numeric operation
                    $subTotalCredit += (float) $item['Credit']; // Cast to ensure numeric operation
                    $headTotalDebit += (float) $item['Debit'];
                    $headTotalCredit += (float) $item['Credit'];
                    $totalDebit += (float) $item['Debit'];
                    $totalCredit += (float) $item['Credit'];

                    $rowCount++;
                }

               // Add sub-total row for this subhead
                $html .= '<tr style="background-color:#e2f3f5; font-weight:bold;">
                <td colspan="4" style="text-align:right;"><strong>Sub Total For ' . $subHeadCount . '</strong></td>
                <td>' . number_format($subTotalDebit, 0) . '</td>
                <td>' . number_format($subTotalCredit, 0) . '</td>
                </tr>'
                . '<tr style="background-color:#e2f3f5; font-weight:bold;">
                <td colspan="4" style="text-align:right;"><strong>Balance For ' . $subHeadCount . '</strong></td>
                <td colspan="2">' . number_format($subTotalDebit + $subTotalCredit, 0) . '</td>
                </tr>';

            }

            // Add head total row after processing all sub-heads under this headCount
            $html .= '<tr style="background-color:#d2edc7; font-weight:bold; color:red;">
            <td colspan="4" style="text-align:right;"><strong>Total For ' . $headCount . '</strong></td>
            <td>' . number_format($headTotalDebit, 0) . '</td>
            <td>' . number_format($headTotalCredit, 0) . '</td>
            </tr>'
                . '<tr style="background-color:#d2edc7; font-weight:bold; color:red;">
                <td colspan="4" style="text-align:right;"><strong>Balance For ' . $headCount . '</strong></td>
                <td colspan="2">' . number_format($headTotalDebit + $headTotalCredit, 0) . '</td>
                </tr>';
        }

        $html .= '</table>'; // Close the main table

        // Output the HTML content to the PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        $filename = "balance_all.pdf";

        // Determine output type
        if ($request->outputType === 'download') {
            $pdf->Output($filename, 'D'); // For download
        } else {
            $pdf->Output($filename, 'I'); // For inline view
        }        
    }

    private function groupByHeadAndSub($data)
    {
        $groupedData = [];

        // Loop through all available heads (keys in the data object)
        foreach ($data as $head => $items) {
            $groupedData[$head] = [];

            // Loop through each item under the current head (Assets or Liabilities, etc.)
            foreach ($items as $item) {
                $sub = $item['sub']; // Get the subhead from each item
                if (!isset($groupedData[$head][$sub])) {
                    $groupedData[$head][$sub] = []; // Initialize subhead if it doesn't exist
                }
                // Push the item into the appropriate subhead category under the head
                $groupedData[$head][$sub][] = $item;
            }
        }

        return $groupedData;
    }
}
