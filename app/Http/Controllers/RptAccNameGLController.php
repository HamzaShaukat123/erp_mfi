<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AC;
use App\Models\lager_much_op_bal;
use App\Models\lager_much_all;
use App\Models\lager_pdc;
use App\Exports\ACNameGLExport;
use App\Exports\ACNameGLRExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\myPDF;
use Carbon\Carbon;

class RptAccNameGLController extends Controller
{
    public function gl(Request $request){
        $lager_much_op_bal = lager_much_op_bal::where('ac1', $request->acc_id)
        ->where('date', '<', $request->fromDate)
        ->get();

        $lager_much_all = lager_much_all::where('account_cod', $request->acc_id)
        ->whereBetween('jv_date', [$request->fromDate, $request->toDate])
        ->orderBy('jv_date','asc')
        ->orderBy('prefix','asc')
        ->orderBy('auto_lager','asc')
        ->get();

        $lager_pdc = lager_pdc::where('ac_cr_sid', $request->acc_id)
        ->whereNull('voch_id')
        ->get();
    
        $response = [
            'lager_much_op_bal' => $lager_much_op_bal,
            'lager_much_all' => $lager_much_all,
            'lager_pdc' => $lager_pdc,
        ];

        return response()->json($response);
    }

    public function glExcel(Request $request)
    {
            
        $request->validate([
            'fromDate' => 'required|date',
            'toDate' => 'required|date',
            'acc_id' => 'required|integer',
        ]);

        // Fetch opening balance records
        $lager_much_op_bal = lager_much_op_bal::where('ac1', $request->acc_id)
            ->join('ac', 'ac.ac_code', '=', 'lager_much_op_bal.ac1')
            ->where('date', '<', $request->fromDate)
            ->get();

        // Fetch transactions within the date range
        $lager_much_all = lager_much_all::where('account_cod', $request->acc_id)
            ->whereBetween('jv_date', [$request->fromDate, $request->toDate])
            ->orderBy('jv_date', 'asc')
            ->orderBy('prefix', 'asc')
            ->orderBy('auto_lager', 'asc')
            ->get();

        // Calculate opening balance
        $SOD = $lager_much_op_bal->sum('SumOfDebit');
        $SOC = $lager_much_op_bal->sum('SumOfrec_cr');
        $opening_bal = $SOD - $SOC;

        // Initialize balance and totals
        $balance = $opening_bal;
        $totalDebit = 0;
        $totalCredit = 0;

        // Prepare data for Excel export
        $rows = [];
        $rows[] = [
            'R/No', 'Date', 'Details', 'Debit', 'Credit', 'Balance',
        ];
        $rows[] = [
            '', '', '+----Opening Balance----+', '', '', number_format($opening_bal, 0),
        ];

        foreach ($lager_much_all as $items) {
            // Update running balance
            $debit = $items->Debit ?? 0;
            $credit = $items->Credit ?? 0;

            $balance += $debit;
            $balance -= $credit;

            $totalDebit += $debit;
            $totalCredit += $credit;

            $rows[] = [
                $items->prefix . $items->auto_lager,
                Carbon::parse($items->jv_date)->format('d-m-y'),
                $items->ac2,
                number_format($debit, 0),
                number_format($credit, 0),
                number_format($balance, 0),
            ];
        }

        // Add totals row
        $rows[] = [
            'Total',
            '',
            '',
            number_format($totalDebit, 0),
            number_format($totalCredit, 0),
            number_format($balance, 0),
        ];

        // Filename
        $filename = "general_ledger_{$request->acc_id}_from_{$request->fromDate}_to_{$request->toDate}.xlsx";

        // Return Excel download
        return Excel::download(new ACNameGLExport($rows), $filename);

    }
    public function glrExcel(Request $request)
    {
            
        $request->validate([
            'fromDate' => 'required|date',
            'toDate' => 'required|date',
            'acc_id' => 'required|integer',
        ]);

        // Fetch opening balance records
        $lager_much_op_bal = lager_much_op_bal::where('ac1', $request->acc_id)
            ->join('ac', 'ac.ac_code', '=', 'lager_much_op_bal.ac1')
            ->where('date', '<', $request->fromDate)
            ->get();

        // Fetch transactions within the date range
        $lager_much_all = lager_much_all::where('account_cod', $request->acc_id)
            ->whereBetween('jv_date', [$request->fromDate, $request->toDate])
            ->orderBy('jv_date', 'asc')
            ->orderBy('prefix', 'asc')
            ->orderBy('auto_lager', 'asc')
            ->get();

        // Calculate opening balance
        $SOD = $lager_much_op_bal->sum('SumOfDebit');
        $SOC = $lager_much_op_bal->sum('SumOfrec_cr');
        $opening_bal = $SOD - $SOC;

        // Initialize balance and totals
        $balance = $opening_bal;
        $totalDebit = 0;
        $totalCredit = 0;

        // Prepare data for Excel export
        $rows = [];
        $rows[] = [
            'R/No', 'Date', 'Details','Narrations', 'Debit', 'Credit', 'Balance',
        ];
        $rows[] = [
            '', '', '+----Opening Balance----+', '', '', number_format($opening_bal, 0),
        ];

        foreach ($lager_much_all as $items) {
            // Update running balance
            $debit = $items->Debit ?? 0;
            $credit = $items->Credit ?? 0;

            $balance += $debit;
            $balance -= $credit;

            $totalDebit += $debit;
            $totalCredit += $credit;

            $rows[] = [
                $items->prefix . $items->auto_lager,
                Carbon::parse($items->jv_date)->format('d-m-y'),
                $items->ac2,
                $items->Narration,
                number_format($debit, 0),
                number_format($credit, 0),
                number_format($balance, 0),
            ];
        }

        // Add totals row
        $rows[] = [
            'Total',
            '',
            '',
            number_format($totalDebit, 0),
            number_format($totalCredit, 0),
            number_format($balance, 0),
        ];

        // Filename
        $filename = "general_ledgerR_{$request->acc_id}_from_{$request->fromDate}_to_{$request->toDate}.xlsx";

        // Return Excel download
        return Excel::download(new ACNameGLRExport($rows), $filename);

    }

    public function glPDF(Request $request){
        // Fetch opening balance records
        $lager_much_op_bal = lager_much_op_bal::where('ac1', $request->acc_id)
        ->join('ac', 'ac.ac_code', '=', 'lager_much_op_bal.ac1')
        ->where('date', '<', $request->fromDate)
        ->get();

        // Fetch transactions within the date range
        $lager_much_all = lager_much_all::where('account_cod', $request->acc_id)
        ->whereBetween('jv_date', [$request->fromDate, $request->toDate])
        ->orderBy('jv_date', 'asc')
        ->orderBy('prefix', 'asc')
        ->orderBy('auto_lager', 'asc')
        ->get();

        $SOD = 0;
        $SOC = 0;

        // Calculate SumOfDebit and SumOfrec_cr for opening balance
        foreach ($lager_much_op_bal as $record) {
        $SOD += $record->SumOfDebit ?? 0;
        $SOC += $record->SumOfrec_cr ?? 0;
        }

        $opening_bal = $SOD - $SOC;
        $balance = $opening_bal; // Start with opening balance
        $totalDebit = 0;
        $totalCredit = 0;

        // Get and format current and report dates
        $currentDate = Carbon::now()->format('d-m-y');
        $formattedFromDate = Carbon::createFromFormat('Y-m-d', $request->fromDate)->format('d-m-y');
        $formattedToDate = Carbon::createFromFormat('Y-m-d', $request->toDate)->format('d-m-y');

        // Initialize PDF
        $pdf = new MyPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('MFI');
        $pdf->SetTitle('General Ledger-' . htmlspecialchars($lager_much_op_bal->first()->ac_name));
        $pdf->SetSubject("General Ledger");
        $pdf->SetKeywords('General Ledger, TCPDF, PDF');
        $pdf->setPageOrientation('P');
        $pdf->AddPage();
        $pdf->setCellPadding(1.2);

        // Document header
        $heading = '<h1 style="font-size:20px;text-align:center;font-style:italic;text-decoration:underline;color:#17365D">General Ledger</h1>';
        $pdf->writeHTML($heading, true, false, true, false, '');

        // Account Info Table
        $html = '
        <table style="border:1px solid #000; width:100%; padding:6px; border-collapse:collapse;">
        <tr>
        <td style="font-size:12px; font-weight:bold; color:#17365D; padding:5px 10px; border-bottom:1px solid #000; width:70%;"> 
            Account Name: <span style="color:black;">' . htmlspecialchars($lager_much_op_bal->first()->ac_name) . '</span>
        </td>
        <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; padding:5px 10px; border-bottom:1px solid #000; border-left:1px solid #000; width:30%;"> 
            Print Date: <span style="color:black;">' . htmlspecialchars($currentDate) . '</span>
        </td>
        </tr>
        <tr>
        <td style="font-size:12px; font-weight:bold; color:#17365D; padding:5px 10px; border-bottom:1px solid #000; width:70%;"> 
            Address: <span style="color:black;">' . htmlspecialchars($lager_much_op_bal->first()->address) . '</span>
        </td>
        <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; padding:5px 10px; border-bottom:1px solid #000; border-left:1px solid #000;width:30%;"> 
            From Date: <span style="color:black;">' . htmlspecialchars($formattedFromDate) . '</span>
        </td>
        </tr>
        <tr>
        <td style="font-size:12px; font-weight:bold; color:#17365D; padding:5px 10px; border-bottom:1px solid #000; width:70%;"> 
            Remarks: <span style="color:black;">' . htmlspecialchars($lager_much_op_bal->first()->remarks) . '</span>
        </td>
        <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; padding:5px 10px; border-bottom:1px solid #000; border-left:1px solid #000; width:30%;"> 
            To Date: <span style="color:black;">' . htmlspecialchars($formattedToDate) . '</span>
        </td>
        </tr>
        </table>';
        $pdf->writeHTML($html, true, false, true, false, '');

        // Build the HTML for the table
        $html = '
        <table border="1" style="border-collapse: collapse; width:100%; text-align:center;">
        <thead>
        <tr>
            <th style="width:13%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">R/No</th>
            <th style="width:12%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Date</th>
            <th style="width:32%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Details</th>
            <th style="width:13%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Debit</th>
            <th style="width:13%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Credit</th>
            <th style="width:17%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Balance</th>
        </tr>
        </thead>
        <tbody>';

        $html .= '
        <tr>
        <td style="width:13%;"></td>
        <td style="width:12%;"></td>
        <td style="text-align:center; font-weight:bold; padding:10px; width:32%;">+----Opening Balance----+</td>
        <td style="width:13%;"></td>
        <td style="width:13%;"></td>
        <td style="text-align:center; padding:10px;; width:17%;">' . number_format($opening_bal, 0) . '</td>
        </tr>';

        // Set a predefined height for the content (adjust based on your content size)
        $tableContentHeight = 20; // Adjust this value based on the row height

        // Loop through data and append rows
        $count = 1;
        foreach ($lager_much_all as $items) {
        // Check if we need to add a new page (based on current position and content height)
        if ($pdf->getY() + $tableContentHeight > $pdf->getPageHeight()) {
            $pdf->AddPage();  // Start a new page
        }

        // Alternate background color between white and light gray
        $bgColor = ($count % 2 == 0) ? '#f1f1f1' : '#ffffff';

        // Update running balance
        if (!empty($items->Debit) && is_numeric($items->Debit)) {
            $balance += $items->Debit;
            $totalDebit += $items->Debit;
        }

        if (!empty($items->Credit) && is_numeric($items->Credit)) {
            $balance -= $items->Credit;
            $totalCredit += $items->Credit;
        }

        // Add row to table with alternating colors
        $html .= '<tr style="background-color:' . $bgColor . ';">
            <td style="width:13%; padding:10px; text-align:center;">' . $items->prefix . $items->auto_lager . '</td>
            <td style="width:12%; padding:10px; text-align:center;">' . Carbon::createFromFormat('Y-m-d', $items->jv_date)->format('d-m-y') . '</td>
            <td style="width:32%; padding:10px; text-align:center; font-size:9px;">' . $items->ac2 . '</td>
            <td style="width:13%; padding:10px; text-align:center;">' . number_format($items->Debit, 0) . '</td>
            <td style="width:13%; padding:10px; text-align:center;">' . number_format($items->Credit, 0) . '</td>
            <td style="width:17%; padding:10px; text-align:center;">' . number_format($balance, 0) . '</td>
        </tr>';
        $count++;
        }

        // Add totals row
        $num_to_words = $pdf->convertCurrencyToWords($balance);
        $html .= '<tr style="background-color:#d9edf7; font-weight:bold;">
        <td colspan="3" style="text-align:center; font-style:italic; padding:10px;">' . htmlspecialchars($num_to_words) . '</td>
        <td style="text-align:center; padding:10px;">' . number_format($totalDebit, 0) . '</td>
        <td style="text-align:center; padding:10px;">' . number_format($totalCredit, 0) . '</td>
        <td style="text-align:center; padding:10px;">' . number_format($balance, 0) . '</td>
        </tr>';

        // Close tbody and table
        $html .= '</tbody></table>';

        // Add space between the tables
        $html .= '<div style="height:20px;"></div>'; // Adds a 20px gap between tables


        // Fetch unadjusted post-dated cheques (replace this query with your actual data retrieval logic)
            $lager_pdc = lager_pdc::where('ac_cr_sid', $request->acc_id)
            ->whereNull('voch_id')
            ->get();

            

            $html .= '
            <table border="1" style="border-collapse: collapse; width:100%; text-align:center; margin-top:5px;">
                <tr style="background-color:#bfe3d0; font-weight:bold;">
                    <td colspan="8" style="text-align:center; padding:10px;">Unadjusted Post Dated Cheques</td>
                </tr>
                <thead>
                    <tr>
                        <th style="width:8%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Sr</th>
                        <th style="width:13%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Voucher#</th>
                        <th style="width:12%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Date</th>
                        <th style="width:16%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Remarks</th>
                        <th style="width:13%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Cheque#</th>
                        <th style="width:12%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Cheque Date</th>
                        <th style="width:13%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Debit</th>
                        <th style="width:13%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Credit</th>
                    </tr>
                </thead>
                <tbody>';
                
        // Initialize variables to store total debit and credit
        $totalDebit2 = 0;
        $totalCredit2 = 0;
        
        // Loop through the unadjusted cheques data and append rows
        $count = 1;
        foreach ($lager_pdc as $cheque) {
            // Alternate background color between white and light gray
            $bgColor = ($count % 2 == 0) ? '#f1f1f1' : '#ffffff';
        
            // Add debit and credit to the totals
            $totalDebit2 += $cheque->Debit;
            $totalCredit2 += $cheque->Credit;
        
            $html .= '
                <tr style="background-color:' . $bgColor . ';">
                    <td style="width:8%; padding:10px; text-align:center;">' . $count . '</td>
                    <td style="width:13%; padding:10px; text-align:center;">' . $cheque->prefix . $cheque->pdc_id . '</td>
                    <td style="width:12%; padding:10px; text-align:center;">' . Carbon::createFromFormat('Y-m-d', $cheque->date)->format('d-m-y') . '</td>
                    <td style="width:16%; padding:10px; text-align:center; font-size:9px;">' . $cheque->remarks . ' ' . $cheque->bankname . '</td>
                    <td style="width:13%; padding:10px; text-align:center; font-size:9px;">' . $cheque->instrumentnumber . '</td>
                    <td style="width:12%; padding:10px; text-align:center;">' . Carbon::createFromFormat('Y-m-d', $cheque->chqdate)->format('d-m-y') . '</td>
                    <td style="width:13%; padding:10px; text-align:center;">' . number_format($cheque->Debit, 0) . '</td>
                    <td style="width:13%; padding:10px; text-align:center;">' . number_format($cheque->Credit, 0) . '</td>
                </tr>';
            $count++;
        }
        
        // If no records are found, display a message
        if ($count === 1) {
            $html .= '
                <tr>
                    <td colspan="8" style="padding:10px; text-align:center; font-style:italic; color:gray;">No pending unadjusted post-dated cheques.</td>
                </tr>';
        }
        
        // Add the totals row
        $html .= '
            <tr style="background-color:#bfe3d0; font-weight:bold;">
                <td colspan="6" style="text-align:right; padding:10px;">Total</td>
                <td style="padding:10px; text-align:center;">' . number_format($totalDebit2, 0) . '</td>
                <td style="padding:10px; text-align:center;">' . number_format($totalCredit2, 0) . '</td>
            </tr>';
        
        $html .= '</tbody></table>';


        // Write HTML content to the PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Filename and Output
        $filename = "general_ledger_of_{$lager_much_op_bal->first()->ac_name}_from_{$formattedFromDate}_to_{$formattedToDate}.pdf";
        $pdf->Output($filename, 'I');
  
    }

    public function glDownload(Request $request){
        // Fetch opening balance records
        $lager_much_op_bal = lager_much_op_bal::where('ac1', $request->acc_id)
        ->join('ac', 'ac.ac_code', '=', 'lager_much_op_bal.ac1')
        ->where('date', '<', $request->fromDate)
        ->get();

        // Fetch transactions within the date range
        $lager_much_all = lager_much_all::where('account_cod', $request->acc_id)
        ->whereBetween('jv_date', [$request->fromDate, $request->toDate])
        ->orderBy('jv_date', 'asc')
        ->orderBy('prefix', 'asc')
        ->orderBy('auto_lager', 'asc')
        ->get();

        $SOD = 0;
        $SOC = 0;

        // Calculate SumOfDebit and SumOfrec_cr for opening balance
        foreach ($lager_much_op_bal as $record) {
        $SOD += $record->SumOfDebit ?? 0;
        $SOC += $record->SumOfrec_cr ?? 0;
        }

        $opening_bal = $SOD - $SOC;
        $balance = $opening_bal; // Start with opening balance
        $totalDebit = 0;
        $totalCredit = 0;

        // Get and format current and report dates
        $currentDate = Carbon::now()->format('d-m-y');
        $formattedFromDate = Carbon::createFromFormat('Y-m-d', $request->fromDate)->format('d-m-y');
        $formattedToDate = Carbon::createFromFormat('Y-m-d', $request->toDate)->format('d-m-y');

        // Initialize PDF
        $pdf = new MyPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('MFI');
        $pdf->SetTitle('General Ledger-' . htmlspecialchars($lager_much_op_bal->first()->ac_name));
        $pdf->SetSubject("General Ledger");
        $pdf->SetKeywords('General Ledger, TCPDF, PDF');
        $pdf->setPageOrientation('P');
        $pdf->AddPage();
        $pdf->setCellPadding(1.2);

        // Document header
        $heading = '<h1 style="font-size:20px;text-align:center;font-style:italic;text-decoration:underline;color:#17365D">General Ledger</h1>';
        $pdf->writeHTML($heading, true, false, true, false, '');

        // Account Info Table
        $html = '
        <table style="border:1px solid #000; width:100%; padding:6px; border-collapse:collapse;">
        <tr>
        <td style="font-size:12px; font-weight:bold; color:#17365D; padding:5px 10px; border-bottom:1px solid #000; width:70%;"> 
            Account Name: <span style="color:black;">' . htmlspecialchars($lager_much_op_bal->first()->ac_name) . '</span>
        </td>
        <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; padding:5px 10px; border-bottom:1px solid #000; border-left:1px solid #000; width:30%;"> 
            Print Date: <span style="color:black;">' . htmlspecialchars($currentDate) . '</span>
        </td>
        </tr>
        <tr>
        <td style="font-size:12px; font-weight:bold; color:#17365D; padding:5px 10px; border-bottom:1px solid #000; width:70%;"> 
            Address: <span style="color:black;">' . htmlspecialchars($lager_much_op_bal->first()->address) . '</span>
        </td>
        <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; padding:5px 10px; border-bottom:1px solid #000; border-left:1px solid #000;width:30%;"> 
            From Date: <span style="color:black;">' . htmlspecialchars($formattedFromDate) . '</span>
        </td>
        </tr>
        <tr>
        <td style="font-size:12px; font-weight:bold; color:#17365D; padding:5px 10px; border-bottom:1px solid #000; width:70%;"> 
            Remarks: <span style="color:black;">' . htmlspecialchars($lager_much_op_bal->first()->remarks) . '</span>
        </td>
        <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; padding:5px 10px; border-bottom:1px solid #000; border-left:1px solid #000; width:30%;"> 
            To Date: <span style="color:black;">' . htmlspecialchars($formattedToDate) . '</span>
        </td>
        </tr>
        </table>';
        $pdf->writeHTML($html, true, false, true, false, '');

        // Build the HTML for the table
        $html = '
        <table border="1" style="border-collapse: collapse; width:100%; text-align:center;">
        <thead>
        <tr>
            <th style="width:13%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">R/No</th>
            <th style="width:12%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Date</th>
            <th style="width:32%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Details</th>
            <th style="width:13%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Debit</th>
            <th style="width:13%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Credit</th>
            <th style="width:17%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Balance</th>
        </tr>
        </thead>
        <tbody>';

        $html .= '
        <tr>
        <td style="width:13%;"></td>
        <td style="width:12%;"></td>
        <td style="text-align:center; font-weight:bold; padding:10px; width:32%;">+----Opening Balance----+</td>
        <td style="width:13%;"></td>
        <td style="width:13%;"></td>
        <td style="text-align:center; padding:10px;; width:17%;">' . number_format($opening_bal, 0) . '</td>
        </tr>';

        // Set a predefined height for the content (adjust based on your content size)
        $tableContentHeight = 20; // Adjust this value based on the row height

        // Loop through data and append rows
        $count = 1;
        foreach ($lager_much_all as $items) {
        // Check if we need to add a new page (based on current position and content height)
        if ($pdf->getY() + $tableContentHeight > $pdf->getPageHeight()) {
            $pdf->AddPage();  // Start a new page
        }

        // Alternate background color between white and light gray
        $bgColor = ($count % 2 == 0) ? '#f1f1f1' : '#ffffff';

        // Update running balance
        if (!empty($items->Debit) && is_numeric($items->Debit)) {
            $balance += $items->Debit;
            $totalDebit += $items->Debit;
        }

        if (!empty($items->Credit) && is_numeric($items->Credit)) {
            $balance -= $items->Credit;
            $totalCredit += $items->Credit;
        }

        // Add row to table with alternating colors
        $html .= '<tr style="background-color:' . $bgColor . ';">
            <td style="width:13%; padding:10px; text-align:center;">' . $items->prefix . $items->auto_lager . '</td>
            <td style="width:12%; padding:10px; text-align:center;">' . Carbon::createFromFormat('Y-m-d', $items->jv_date)->format('d-m-y') . '</td>
            <td style="width:32%; padding:10px; text-align:center; font-size:9px;">' . $items->ac2 . '</td>
            <td style="width:13%; padding:10px; text-align:center;">' . number_format($items->Debit, 0) . '</td>
            <td style="width:13%; padding:10px; text-align:center;">' . number_format($items->Credit, 0) . '</td>
            <td style="width:17%; padding:10px; text-align:center;">' . number_format($balance, 0) . '</td>
        </tr>';
        $count++;
        }

        // Add totals row
        $num_to_words = $pdf->convertCurrencyToWords($balance);
        $html .= '<tr style="background-color:#d9edf7; font-weight:bold;">
        <td colspan="3" style="text-align:center; font-style:italic; padding:10px;">' . htmlspecialchars($num_to_words) . '</td>
        <td style="text-align:center; padding:10px;">' . number_format($totalDebit, 0) . '</td>
        <td style="text-align:center; padding:10px;">' . number_format($totalCredit, 0) . '</td>
        <td style="text-align:center; padding:10px;">' . number_format($balance, 0) . '</td>
        </tr>';

        // Close tbody and table
        $html .= '</tbody></table>';

        // Add space between the tables
        $html .= '<div style="height:20px;"></div>'; // Adds a 20px gap between tables


        // Fetch unadjusted post-dated cheques (replace this query with your actual data retrieval logic)
            $lager_pdc = lager_pdc::where('ac_cr_sid', $request->acc_id)
            ->whereNull('voch_id')
            ->get();

            

            $html .= '
            <table border="1" style="border-collapse: collapse; width:100%; text-align:center; margin-top:5px;">
                <tr style="background-color:#bfe3d0; font-weight:bold;">
                    <td colspan="8" style="text-align:center; padding:10px;">Unadjusted Post Dated Cheques</td>
                </tr>
                <thead>
                    <tr>
                        <th style="width:8%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Sr</th>
                        <th style="width:13%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Voucher#</th>
                        <th style="width:12%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Date</th>
                        <th style="width:16%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Remarks</th>
                        <th style="width:13%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Cheque#</th>
                        <th style="width:12%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Cheque Date</th>
                        <th style="width:13%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Debit</th>
                        <th style="width:13%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Credit</th>
                    </tr>
                </thead>
                <tbody>';
                
        // Initialize variables to store total debit and credit
        $totalDebit2 = 0;
        $totalCredit2 = 0;
        
        // Loop through the unadjusted cheques data and append rows
        $count = 1;
        foreach ($lager_pdc as $cheque) {
            // Alternate background color between white and light gray
            $bgColor = ($count % 2 == 0) ? '#f1f1f1' : '#ffffff';
        
            // Add debit and credit to the totals
            $totalDebit2 += $cheque->Debit;
            $totalCredit2 += $cheque->Credit;
        
            $html .= '
                <tr style="background-color:' . $bgColor . ';">
                    <td style="width:8%; padding:10px; text-align:center;">' . $count . '</td>
                    <td style="width:13%; padding:10px; text-align:center;">' . $cheque->prefix . $cheque->pdc_id . '</td>
                    <td style="width:12%; padding:10px; text-align:center;">' . Carbon::createFromFormat('Y-m-d', $cheque->date)->format('d-m-y') . '</td>
                    <td style="width:16%; padding:10px; text-align:center; font-size:9px;">' . $cheque->remarks . ' ' . $cheque->bankname . '</td>
                    <td style="width:13%; padding:10px; text-align:center; font-size:9px;">' . $cheque->instrumentnumber . '</td>
                    <td style="width:12%; padding:10px; text-align:center;">' . Carbon::createFromFormat('Y-m-d', $cheque->chqdate)->format('d-m-y') . '</td>
                    <td style="width:13%; padding:10px; text-align:center;">' . number_format($cheque->Debit, 0) . '</td>
                    <td style="width:13%; padding:10px; text-align:center;">' . number_format($cheque->Credit, 0) . '</td>
                </tr>';
            $count++;
        }
        
        // If no records are found, display a message
        if ($count === 1) {
            $html .= '
                <tr>
                    <td colspan="8" style="padding:10px; text-align:center; font-style:italic; color:gray;">No pending unadjusted post-dated cheques.</td>
                </tr>';
        }
        
        // Add the totals row
        $html .= '
            <tr style="background-color:#bfe3d0; font-weight:bold;">
                <td colspan="6" style="text-align:right; padding:10px;">Total</td>
                <td style="padding:10px; text-align:center;">' . number_format($totalDebit2, 0) . '</td>
                <td style="padding:10px; text-align:center;">' . number_format($totalCredit2, 0) . '</td>
            </tr>';
        
        $html .= '</tbody></table>';


        // Write HTML content to the PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Filename and Output
        $filename = "general_ledger_of_{$lager_much_op_bal->first()->ac_name}_from_{$formattedFromDate}_to_{$formattedToDate}.pdf";
        $pdf->Output($filename, 'D');
  
    }

    public function glr(Request $request){
        $lager_much_op_bal = lager_much_op_bal::where('ac1', $request->acc_id)
        ->where('date', '<', $request->fromDate)
        ->get();

        $lager_much_all = lager_much_all::where('account_cod', $request->acc_id)
        ->whereBetween('jv_date', [$request->fromDate, $request->toDate])
        ->orderBy('jv_date','asc')
        ->orderBy('prefix','asc')
        ->orderBy('auto_lager','asc')
        ->get();

        $lager_pdc = lager_pdc::where('ac_cr_sid', $request->acc_id)
        ->whereNull('voch_id')
        ->get();
    
        $response = [
            'lager_much_op_bal' => $lager_much_op_bal,
            'lager_much_all' => $lager_much_all,
            'lager_pdc' => $lager_pdc,
        ];

        return response()->json($response);
    }


    public function glrPDF(Request $request) {
      // Fetch opening balance records
        $lager_much_op_bal = lager_much_op_bal::where('ac1', $request->acc_id)
        ->join('ac', 'ac.ac_code', '=', 'lager_much_op_bal.ac1')
        ->where('date', '<', $request->fromDate)
        ->get();

        // Fetch transactions within the date range
        $lager_much_all = lager_much_all::where('account_cod', $request->acc_id)
        ->whereBetween('jv_date', [$request->fromDate, $request->toDate])
        ->orderBy('jv_date', 'asc')
        ->orderBy('prefix', 'asc')
        ->orderBy('auto_lager', 'asc')
        ->get();

        $SOD = 0;
        $SOC = 0;

        // Calculate SumOfDebit and SumOfrec_cr for opening balance
        foreach ($lager_much_op_bal as $record) {
        $SOD += $record->SumOfDebit ?? 0;
        $SOC += $record->SumOfrec_cr ?? 0;
        }

        $opening_bal = $SOD - $SOC;
        $balance = $opening_bal; // Start with opening balance
        $totalDebit = 0;
        $totalCredit = 0;

        // Get and format current and report dates
        $currentDate = Carbon::now()->format('d-m-y');
        $formattedFromDate = Carbon::createFromFormat('Y-m-d', $request->fromDate)->format('d-m-y');
        $formattedToDate = Carbon::createFromFormat('Y-m-d', $request->toDate)->format('d-m-y');

        // Initialize PDF
        $pdf = new MyPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('MFI');
        $pdf->SetTitle('General Ledger R-' . htmlspecialchars($lager_much_op_bal->first()->ac_name));
        $pdf->SetSubject("General Ledger R");
        $pdf->SetKeywords('General Ledger R, TCPDF, PDF');
        $pdf->setPageOrientation('P');
        $pdf->AddPage();
        $pdf->setCellPadding(1.2);

        // Document header
        $heading = '<h1 style="font-size:20px;text-align:center;font-style:italic;text-decoration:underline;color:#17365D">General Ledger R</h1>';
        $pdf->writeHTML($heading, true, false, true, false, '');

        // Account Info Table
        $html = '
        <table style="border:1px solid #000; width:100%; padding:6px; border-collapse:collapse;">
        <tr>
        <td style="font-size:12px; font-weight:bold; color:#17365D; padding:5px 10px; border-bottom:1px solid #000; width:70%;"> 
            Account Name: <span style="color:black;">' . htmlspecialchars($lager_much_op_bal->first()->ac_name) . '</span>
        </td>
        <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; padding:5px 10px; border-bottom:1px solid #000; border-left:1px solid #000; width:30%;"> 
            Print Date: <span style="color:black;">' . htmlspecialchars($currentDate) . '</span>
        </td>
        </tr>
        <tr>
        <td style="font-size:12px; font-weight:bold; color:#17365D; padding:5px 10px; border-bottom:1px solid #000; width:70%;"> 
            Address: <span style="color:black;">' . htmlspecialchars($lager_much_op_bal->first()->address) . '</span>
        </td>
        <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; padding:5px 10px; border-bottom:1px solid #000; border-left:1px solid #000;width:30%;"> 
            From Date: <span style="color:black;">' . htmlspecialchars($formattedFromDate) . '</span>
        </td>
        </tr>
        <tr>
        <td style="font-size:12px; font-weight:bold; color:#17365D; padding:5px 10px; border-bottom:1px solid #000; width:70%;"> 
            Remarks: <span style="color:black;">' . htmlspecialchars($lager_much_op_bal->first()->remarks) . '</span>
        </td>
        <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; padding:5px 10px; border-bottom:1px solid #000; border-left:1px solid #000; width:30%;"> 
            To Date: <span style="color:black;">' . htmlspecialchars($formattedToDate) . '</span>
        </td>
        </tr>
        </table>';
        $pdf->writeHTML($html, true, false, true, false, '');

        // Build the HTML for the table
        $html = '
        <table border="1" style="border-collapse: collapse; width:100%; text-align:center;">
        <thead>
        <tr>
            <th style="width:13%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">R/No</th>
            <th style="width:12%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Date</th>
            <th style="width:32%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Details</th>
            <th style="width:13%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Debit</th>
            <th style="width:13%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Credit</th>
            <th style="width:17%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Balance</th>
        </tr>
        </thead>
        <tbody>';

        $html .= '
        <tr>
        <td style="width:13%;"></td>
        <td style="width:12%;"></td>
        <td style="text-align:center; font-weight:bold; padding:10px; width:32%;">+----Opening Balance----+</td>
        <td style="width:13%;"></td>
        <td style="width:13%;"></td>
        <td style="text-align:center; padding:10px;; width:17%;">' . number_format($opening_bal, 0) . '</td>
        </tr>';

        // Set a predefined height for the content (adjust based on your content size)
        $tableContentHeight = 20; // Adjust this value based on the row height

        // Loop through data and append rows
        $count = 1;
        foreach ($lager_much_all as $items) {
        // Check if we need to add a new page (based on current position and content height)
        if ($pdf->getY() + $tableContentHeight > $pdf->getPageHeight()) {
            $pdf->AddPage();  // Start a new page
        }

        // Alternate background color between white and light gray
        $bgColor = ($count % 2 == 0) ? '#f1f1f1' : '#ffffff';

        // Update running balance
        if (!empty($items->Debit) && is_numeric($items->Debit)) {
            $balance += $items->Debit;
            $totalDebit += $items->Debit;
        }

        if (!empty($items->Credit) && is_numeric($items->Credit)) {
            $balance -= $items->Credit;
            $totalCredit += $items->Credit;
        }

        // Add row to table with alternating colors
        $html .= '<tr style="background-color:' . $bgColor . ';">
            <td style="width:13%; padding:10px; text-align:center;">' . $items->prefix . $items->auto_lager . '</td>
            <td style="width:12%; padding:10px; text-align:center;">' . Carbon::createFromFormat('Y-m-d', $items->jv_date)->format('d-m-y') . '</td>
            <td style="width:32%; padding:10px; text-align:center; font-size:9px;">' . $items->ac2 . ' ' . $items->Narration . '</td>
            <td style="width:13%; padding:10px; text-align:center;">' . number_format($items->Debit, 0) . '</td>
            <td style="width:13%; padding:10px; text-align:center;">' . number_format($items->Credit, 0) . '</td>
            <td style="width:17%; padding:10px; text-align:center;">' . number_format($balance, 0) . '</td>
        </tr>';
        $count++;
        }

        // Add totals row
        $num_to_words = $pdf->convertCurrencyToWords($balance);
        $html .= '<tr style="background-color:#d9edf7; font-weight:bold;">
        <td colspan="3" style="text-align:center; font-style:italic; padding:10px;">' . htmlspecialchars($num_to_words) . '</td>
        <td style="text-align:center; padding:10px;">' . number_format($totalDebit, 0) . '</td>
        <td style="text-align:center; padding:10px;">' . number_format($totalCredit, 0) . '</td>
        <td style="text-align:center; padding:10px;">' . number_format($balance, 0) . '</td>
        </tr>';

        // Close tbody and table
        $html .= '</tbody></table>';

        // Add space between the tables
        $html .= '<div style="height:20px;"></div>'; // Adds a 20px gap between tables


        // Fetch unadjusted post-dated cheques (replace this query with your actual data retrieval logic)
            $lager_pdc = lager_pdc::where('ac_cr_sid', $request->acc_id)
            ->whereNull('voch_id')
            ->get();

            

            $html .= '
            <table border="1" style="border-collapse: collapse; width:100%; text-align:center; margin-top:5px;">
                <tr style="background-color:#bfe3d0; font-weight:bold;">
                    <td colspan="8" style="text-align:center; padding:10px;">Unadjusted Post Dated Cheques</td>
                </tr>
                <thead>
                    <tr>
                        <th style="width:8%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Sr</th>
                        <th style="width:13%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Voucher#</th>
                        <th style="width:12%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Date</th>
                        <th style="width:16%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Remarks</th>
                        <th style="width:13%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Cheque#</th>
                        <th style="width:12%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Cheque Date</th>
                        <th style="width:13%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Debit</th>
                        <th style="width:13%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Credit</th>
                    </tr>
                </thead>
                <tbody>';
                
        // Initialize variables to store total debit and credit
        $totalDebit2 = 0;
        $totalCredit2 = 0;
        
        // Loop through the unadjusted cheques data and append rows
        $count = 1;
        foreach ($lager_pdc as $cheque) {
            // Alternate background color between white and light gray
            $bgColor = ($count % 2 == 0) ? '#f1f1f1' : '#ffffff';
        
            // Add debit and credit to the totals
            $totalDebit2 += $cheque->Debit;
            $totalCredit2 += $cheque->Credit;
        
            $html .= '
                <tr style="background-color:' . $bgColor . ';">
                    <td style="width:8%; padding:10px; text-align:center;">' . $count . '</td>
                    <td style="width:13%; padding:10px; text-align:center;">' . $cheque->prefix . $cheque->pdc_id . '</td>
                    <td style="width:12%; padding:10px; text-align:center;">' . Carbon::createFromFormat('Y-m-d', $cheque->date)->format('d-m-y') . '</td>
                    <td style="width:16%; padding:10px; text-align:center; font-size:9px;">' . $cheque->remarks . ' ' . $cheque->bankname . '</td>
                    <td style="width:13%; padding:10px; text-align:center; font-size:9px;">' . $cheque->instrumentnumber . '</td>
                    <td style="width:12%; padding:10px; text-align:center;">' . Carbon::createFromFormat('Y-m-d', $cheque->chqdate)->format('d-m-y') . '</td>
                    <td style="width:13%; padding:10px; text-align:center;">' . number_format($cheque->Debit, 0) . '</td>
                    <td style="width:13%; padding:10px; text-align:center;">' . number_format($cheque->Credit, 0) . '</td>
                </tr>';
            $count++;
        }
        
        // If no records are found, display a message
        if ($count === 1) {
            $html .= '
                <tr>
                    <td colspan="8" style="padding:10px; text-align:center; font-style:italic; color:gray;">No pending unadjusted post-dated cheques.</td>
                </tr>';
        }
        
        // Add the totals row
        $html .= '
            <tr style="background-color:#bfe3d0; font-weight:bold;">
                <td colspan="6" style="text-align:right; padding:10px;">Total</td>
                <td style="padding:10px; text-align:center;">' . number_format($totalDebit2, 0) . '</td>
                <td style="padding:10px; text-align:center;">' . number_format($totalCredit2, 0) . '</td>
            </tr>';
        
        $html .= '</tbody></table>';


        // Write HTML content to the PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Filename and Output
        $filename = "general_ledger_r_of_{$lager_much_op_bal->first()->ac_name}_from_{$formattedFromDate}_to_{$formattedToDate}.pdf";
        $pdf->Output($filename, 'I');

    }
    

    public function glrDownload(Request $request){
        // Fetch opening balance records
        $lager_much_op_bal = lager_much_op_bal::where('ac1', $request->acc_id)
        ->join('ac', 'ac.ac_code', '=', 'lager_much_op_bal.ac1')
        ->where('date', '<', $request->fromDate)
        ->get();

        // Fetch transactions within the date range
        $lager_much_all = lager_much_all::where('account_cod', $request->acc_id)
        ->whereBetween('jv_date', [$request->fromDate, $request->toDate])
        ->orderBy('jv_date', 'asc')
        ->orderBy('prefix', 'asc')
        ->orderBy('auto_lager', 'asc')
        ->get();

        $SOD = 0;
        $SOC = 0;

        // Calculate SumOfDebit and SumOfrec_cr for opening balance
        foreach ($lager_much_op_bal as $record) {
        $SOD += $record->SumOfDebit ?? 0;
        $SOC += $record->SumOfrec_cr ?? 0;
        }

        $opening_bal = $SOD - $SOC;
        $balance = $opening_bal; // Start with opening balance
        $totalDebit = 0;
        $totalCredit = 0;

        // Get and format current and report dates
        $currentDate = Carbon::now()->format('d-m-y');
        $formattedFromDate = Carbon::createFromFormat('Y-m-d', $request->fromDate)->format('d-m-y');
        $formattedToDate = Carbon::createFromFormat('Y-m-d', $request->toDate)->format('d-m-y');

        // Initialize PDF
        $pdf = new MyPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('MFI');
        $pdf->SetTitle('General Ledger R-' . htmlspecialchars($lager_much_op_bal->first()->ac_name));
        $pdf->SetSubject("General Ledger R");
        $pdf->SetKeywords('General Ledger R, TCPDF, PDF');
        $pdf->setPageOrientation('P');
        $pdf->AddPage();
        $pdf->setCellPadding(1.2);

        // Document header
        $heading = '<h1 style="font-size:20px;text-align:center;font-style:italic;text-decoration:underline;color:#17365D">General Ledger R</h1>';
        $pdf->writeHTML($heading, true, false, true, false, '');

        // Account Info Table
        $html = '
        <table style="border:1px solid #000; width:100%; padding:6px; border-collapse:collapse;">
        <tr>
        <td style="font-size:12px; font-weight:bold; color:#17365D; padding:5px 10px; border-bottom:1px solid #000; width:70%;"> 
            Account Name: <span style="color:black;">' . htmlspecialchars($lager_much_op_bal->first()->ac_name) . '</span>
        </td>
        <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; padding:5px 10px; border-bottom:1px solid #000; border-left:1px solid #000; width:30%;"> 
            Print Date: <span style="color:black;">' . htmlspecialchars($currentDate) . '</span>
        </td>
        </tr>
        <tr>
        <td style="font-size:12px; font-weight:bold; color:#17365D; padding:5px 10px; border-bottom:1px solid #000; width:70%;"> 
            Address: <span style="color:black;">' . htmlspecialchars($lager_much_op_bal->first()->address) . '</span>
        </td>
        <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; padding:5px 10px; border-bottom:1px solid #000; border-left:1px solid #000;width:30%;"> 
            From Date: <span style="color:black;">' . htmlspecialchars($formattedFromDate) . '</span>
        </td>
        </tr>
        <tr>
        <td style="font-size:12px; font-weight:bold; color:#17365D; padding:5px 10px; border-bottom:1px solid #000; width:70%;"> 
            Remarks: <span style="color:black;">' . htmlspecialchars($lager_much_op_bal->first()->remarks) . '</span>
        </td>
        <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; padding:5px 10px; border-bottom:1px solid #000; border-left:1px solid #000; width:30%;"> 
            To Date: <span style="color:black;">' . htmlspecialchars($formattedToDate) . '</span>
        </td>
        </tr>
        </table>';
        $pdf->writeHTML($html, true, false, true, false, '');

        // Build the HTML for the table
        $html = '
        <table border="1" style="border-collapse: collapse; width:100%; text-align:center;">
        <thead>
        <tr>
            <th style="width:13%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">R/No</th>
            <th style="width:12%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Date</th>
            <th style="width:32%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Details</th>
            <th style="width:13%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Debit</th>
            <th style="width:13%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Credit</th>
            <th style="width:17%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Balance</th>
        </tr>
        </thead>
        <tbody>';

        $html .= '
        <tr>
        <td style="width:13%;"></td>
        <td style="width:12%;"></td>
        <td style="text-align:center; font-weight:bold; padding:10px; width:32%;">+----Opening Balance----+</td>
        <td style="width:13%;"></td>
        <td style="width:13%;"></td>
        <td style="text-align:center; padding:10px;; width:17%;">' . number_format($opening_bal, 0) . '</td>
        </tr>';

        // Set a predefined height for the content (adjust based on your content size)
        $tableContentHeight = 20; // Adjust this value based on the row height

        // Loop through data and append rows
        $count = 1;
        foreach ($lager_much_all as $items) {
        // Check if we need to add a new page (based on current position and content height)
        if ($pdf->getY() + $tableContentHeight > $pdf->getPageHeight()) {
            $pdf->AddPage();  // Start a new page
        }

        // Alternate background color between white and light gray
        $bgColor = ($count % 2 == 0) ? '#f1f1f1' : '#ffffff';

        // Update running balance
        if (!empty($items->Debit) && is_numeric($items->Debit)) {
            $balance += $items->Debit;
            $totalDebit += $items->Debit;
        }

        if (!empty($items->Credit) && is_numeric($items->Credit)) {
            $balance -= $items->Credit;
            $totalCredit += $items->Credit;
        }

        // Add row to table with alternating colors
        $html .= '<tr style="background-color:' . $bgColor . ';">
            <td style="width:13%; padding:10px; text-align:center;">' . $items->prefix . $items->auto_lager . '</td>
            <td style="width:12%; padding:10px; text-align:center;">' . Carbon::createFromFormat('Y-m-d', $items->jv_date)->format('d-m-y') . '</td>
            <td style="width:32%; padding:10px; text-align:center; font-size:9px;">' . $items->ac2 . ' ' . $items->Narration . '</td>
            <td style="width:13%; padding:10px; text-align:center;">' . number_format($items->Debit, 0) . '</td>
            <td style="width:13%; padding:10px; text-align:center;">' . number_format($items->Credit, 0) . '</td>
            <td style="width:17%; padding:10px; text-align:center;">' . number_format($balance, 0) . '</td>
        </tr>';
        $count++;
        }

        // Add totals row
        $num_to_words = $pdf->convertCurrencyToWords($balance);
        $html .= '<tr style="background-color:#d9edf7; font-weight:bold;">
        <td colspan="3" style="text-align:center; font-style:italic; padding:10px;">' . htmlspecialchars($num_to_words) . '</td>
        <td style="text-align:center; padding:10px;">' . number_format($totalDebit, 0) . '</td>
        <td style="text-align:center; padding:10px;">' . number_format($totalCredit, 0) . '</td>
        <td style="text-align:center; padding:10px;">' . number_format($balance, 0) . '</td>
        </tr>';

        // Close tbody and table
        $html .= '</tbody></table>';

        // Add space between the tables
        $html .= '<div style="height:20px;"></div>'; // Adds a 20px gap between tables


        // Fetch unadjusted post-dated cheques (replace this query with your actual data retrieval logic)
            $lager_pdc = lager_pdc::where('ac_cr_sid', $request->acc_id)
            ->whereNull('voch_id')
            ->get();

            

            $html .= '
            <table border="1" style="border-collapse: collapse; width:100%; text-align:center; margin-top:5px;">
                <tr style="background-color:#bfe3d0; font-weight:bold;">
                    <td colspan="8" style="text-align:center; padding:10px;">Unadjusted Post Dated Cheques</td>
                </tr>
                <thead>
                    <tr>
                        <th style="width:8%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Sr</th>
                        <th style="width:13%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Voucher#</th>
                        <th style="width:12%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Date</th>
                        <th style="width:16%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Remarks</th>
                        <th style="width:13%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Cheque#</th>
                        <th style="width:12%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Cheque Date</th>
                        <th style="width:13%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Debit</th>
                        <th style="width:13%; color:#17365D; font-weight:bold; text-align:center; padding:10px;">Credit</th>
                    </tr>
                </thead>
                <tbody>';
                
        // Initialize variables to store total debit and credit
        $totalDebit2 = 0;
        $totalCredit2 = 0;
        
        // Loop through the unadjusted cheques data and append rows
        $count = 1;
        foreach ($lager_pdc as $cheque) {
            // Alternate background color between white and light gray
            $bgColor = ($count % 2 == 0) ? '#f1f1f1' : '#ffffff';
        
            // Add debit and credit to the totals
            $totalDebit2 += $cheque->Debit;
            $totalCredit2 += $cheque->Credit;
        
            $html .= '
                <tr style="background-color:' . $bgColor . ';">
                    <td style="width:8%; padding:10px; text-align:center;">' . $count . '</td>
                    <td style="width:13%; padding:10px; text-align:center;">' . $cheque->prefix . $cheque->pdc_id . '</td>
                    <td style="width:12%; padding:10px; text-align:center;">' . Carbon::createFromFormat('Y-m-d', $cheque->date)->format('d-m-y') . '</td>
                    <td style="width:16%; padding:10px; text-align:center; font-size:9px;">' . $cheque->remarks . ' ' . $cheque->bankname . '</td>
                    <td style="width:13%; padding:10px; text-align:center; font-size:9px;">' . $cheque->instrumentnumber . '</td>
                    <td style="width:12%; padding:10px; text-align:center;">' . Carbon::createFromFormat('Y-m-d', $cheque->chqdate)->format('d-m-y') . '</td>
                    <td style="width:13%; padding:10px; text-align:center;">' . number_format($cheque->Debit, 0) . '</td>
                    <td style="width:13%; padding:10px; text-align:center;">' . number_format($cheque->Credit, 0) . '</td>
                </tr>';
            $count++;
        }
        
        // If no records are found, display a message
        if ($count === 1) {
            $html .= '
                <tr>
                    <td colspan="8" style="padding:10px; text-align:center; font-style:italic; color:gray;">No pending unadjusted post-dated cheques.</td>
                </tr>';
        }
        
        // Add the totals row
        $html .= '
            <tr style="background-color:#bfe3d0; font-weight:bold;">
                <td colspan="6" style="text-align:right; padding:10px;">Total</td>
                <td style="padding:10px; text-align:center;">' . number_format($totalDebit2, 0) . '</td>
                <td style="padding:10px; text-align:center;">' . number_format($totalCredit2, 0) . '</td>
            </tr>';
        
        $html .= '</tbody></table>';


        // Write HTML content to the PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Filename and Output
        $filename = "general_ledger_r_of_{$lager_much_op_bal->first()->ac_name}_from_{$formattedFromDate}_to_{$formattedToDate}.pdf";
        $pdf->Output($filename, 'D');
  
    }
    
}   
