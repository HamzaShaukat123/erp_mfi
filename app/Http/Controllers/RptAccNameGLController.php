<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AC;
use App\Models\lager_much_op_bal;
use App\Models\lager_much_all;
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
    
        $response = [
            'lager_much_op_bal' => $lager_much_op_bal,
            'lager_much_all' => $lager_much_all,
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
            ->orderBy('jv_date','asc')
            ->orderBy('prefix','asc')
             ->orderBy('auto_lager','asc')
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
        $pdf->SetTitle('General Ledger-' . $lager_much_op_bal[0]['ac_name']);
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
                        Account Name: <span style="color:black;">' . htmlspecialchars($lager_much_op_bal[0]['ac_name']) . '</span>
                    </td>
                    <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; padding:5px 10px; border-bottom:1px solid #000; border-left:1px solid #000; width:30%;">
                        Print Date: <span style="color:black;">' . htmlspecialchars($currentDate) . '</span>
                    </td>
                </tr>
                <tr>
                    <td style="font-size:12px; font-weight:bold; color:#17365D; padding:5px 10px; border-bottom:1px solid #000; width:70%;">
                        Address: <span style="color:black;">' . htmlspecialchars($lager_much_op_bal[0]['address']) . '</span>
                    </td>
                    <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; padding:5px 10px; border-bottom:1px solid #000; border-left:1px solid #000;width:30%;">
                        From Date: <span style="color:black;">' . htmlspecialchars($formattedFromDate) . '</span>
                    </td>
                </tr>
                <tr>
                    <td style="font-size:12px; font-weight:bold; color:#17365D; padding:5px 10px; border-bottom:1px solid #000; width:70%;">
                        Remarks: <span style="color:black;">' . htmlspecialchars($lager_much_op_bal[0]['remarks']) . '</span>
                    </td>
                    <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; padding:5px 10px; border-bottom:1px solid #000; border-left:1px solid #000; width:30%;">
                        To Date: <span style="color:black;">' . htmlspecialchars($formattedToDate) . '</span>
                    </td>
                </tr>
            </table>';
        $pdf->writeHTML($html, true, false, true, false, '');
    
        // Table Headers
            $html = '<table border="1" style="border-collapse: collapse;text-align:center">
            <tr>
                <th style="width:13%;color:#17365D;font-weight:bold;">R/No</th>
                <th style="width:12%;color:#17365D;font-weight:bold;">Date</th>
                <th style="width:32%;color:#17365D;font-weight:bold;">Details</th>
                <th style="width:13%;color:#17365D;font-weight:bold;">Debit</th>
                <th style="width:13%;color:#17365D;font-weight:bold;">Credit</th>
                <th style="width:17%;color:#17365D;font-weight:bold;">Balance</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th  style="text-align: center;font-weight:bold;">+----Opening Balance----+</th>
                <th></th>
                <th></th>
                <th style="text-align: center">' . number_format($opening_bal, 0) . '</th>
            </tr>';
        
        $count = 1;
        foreach ($lager_much_all as $items) {
            $bgColor = ($count % 2 == 0) ? '#f1f1f1' : '#ffffff';
        
            // Update running balance
            if (!empty($items['Debit']) && is_numeric($items['Debit'])) {
                $balance += $items['Debit'];
                $totalDebit += $items['Debit'];
            }
        
            if (!empty($items['Credit']) && is_numeric($items['Credit'])) {
                $balance -= $items['Credit'];
                $totalCredit += $items['Credit'];
            }
        
            // Add row with merged Account Name & Remarks column
            $html .= '<tr style="background-color:' . $bgColor . ';">
                <td>' . $items['prefix'] . '' . $items['auto_lager'] . '</td>
                <td>' . Carbon::createFromFormat('Y-m-d', $items['jv_date'])->format('d-m-y') . '</td>
                <td>' . $items['ac2'] . '</td>
                <td>' . number_format($items['Debit'], 0) . '</td>
                <td>' . number_format($items['Credit'], 0) . '</td>
                <td>' . number_format($balance, 0) . '</td>
            </tr>';
            $count++;
        }
        
        // Add totals row
        $num_to_words = $pdf->convertCurrencyToWords($balance);
        $html .= '<tr style="background-color:#d9edf7; font-weight:bold;">
                    <td colspan="3" style="text-align:center; font-style:italic;"> ' . htmlspecialchars($num_to_words) . '</td>
                    <td style="width:13%;">' . number_format($totalDebit, 0) . '</td>
                    <td style="width:13%;">' . number_format($totalCredit, 0) . '</td>
                    <td style="width:17%;">' . number_format($balance, 0) . '</td>
                </tr>';
        
        $html .= '</table>';
        $pdf->writeHTML($html, true, false, true, false, '');
    
        // Filename and Output
        $filename = "general_ledger_of_{$lager_much_op_bal[0]['ac_name']}_from_{$formattedFromDate}_to_{$formattedToDate}.pdf";
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
            ->orderBy('jv_date','asc')
            ->orderBy('prefix','asc')
            ->orderBy('auto_lager','asc')
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
        $pdf->SetTitle('General Ledger-' . $lager_much_op_bal[0]['ac_name']);
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
                        Account Name: <span style="color:black;">' . htmlspecialchars($lager_much_op_bal[0]['ac_name']) . '</span>
                    </td>
                    <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; padding:5px 10px; border-bottom:1px solid #000; border-left:1px solid #000; width:30%;">
                        Print Date: <span style="color:black;">' . htmlspecialchars($currentDate) . '</span>
                    </td>
                </tr>
                <tr>
                    <td style="font-size:12px; font-weight:bold; color:#17365D; padding:5px 10px; border-bottom:1px solid #000; width:70%;">
                        Address: <span style="color:black;">' . htmlspecialchars($lager_much_op_bal[0]['address']) . '</span>
                    </td>
                    <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; padding:5px 10px; border-bottom:1px solid #000; border-left:1px solid #000;width:30%;">
                        From Date: <span style="color:black;">' . htmlspecialchars($formattedFromDate) . '</span>
                    </td>
                </tr>
                <tr>
                    <td style="font-size:12px; font-weight:bold; color:#17365D; padding:5px 10px; border-bottom:1px solid #000; width:70%;">
                        Remarks: <span style="color:black;">' . htmlspecialchars($lager_much_op_bal[0]['remarks']) . '</span>
                    </td>
                    <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; padding:5px 10px; border-bottom:1px solid #000; border-left:1px solid #000; width:30%;">
                        To Date: <span style="color:black;">' . htmlspecialchars($formattedToDate) . '</span>
                    </td>
                </tr>
            </table>';
        $pdf->writeHTML($html, true, false, true, false, '');
    
        // Table Headers
            $html = '<table border="1" style="border-collapse: collapse;text-align:center">
            <tr>
                <th style="width:13%;color:#17365D;font-weight:bold;">R/No</th>
                <th style="width:12%;color:#17365D;font-weight:bold;">Date</th>
                <th style="width:32%;color:#17365D;font-weight:bold;">Details</th>
                <th style="width:13%;color:#17365D;font-weight:bold;">Debit</th>
                <th style="width:13%;color:#17365D;font-weight:bold;">Credit</th>
                <th style="width:17%;color:#17365D;font-weight:bold;">Balance</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th  style="text-align: center;font-weight:bold;">+----Opening Balance----+</th>
                <th></th>
                <th></th>
                <th style="text-align: center">' . number_format($opening_bal, 0) . '</th>
            </tr>';
        
        $count = 1;
        foreach ($lager_much_all as $items) {
            $bgColor = ($count % 2 == 0) ? '#f1f1f1' : '#ffffff';
        
            // Update running balance
            if (!empty($items['Debit']) && is_numeric($items['Debit'])) {
                $balance += $items['Debit'];
                $totalDebit += $items['Debit'];
            }
        
            if (!empty($items['Credit']) && is_numeric($items['Credit'])) {
                $balance -= $items['Credit'];
                $totalCredit += $items['Credit'];
            }
        
            // Add row with merged Account Name & Remarks column
            $html .= '<tr style="background-color:' . $bgColor . ';">
                <td>' . $items['prefix'] . '' . $items['auto_lager'] . '</td>
                <td>' . Carbon::createFromFormat('Y-m-d', $items['jv_date'])->format('d-m-y') . '</td>
                <td>' . $items['ac2'] . '</td>
                <td>' . number_format($items['Debit'], 0) . '</td>
                <td>' . number_format($items['Credit'], 0) . '</td>
                <td>' . number_format($balance, 0) . '</td>
            </tr>';
            $count++;
        }
        
        // Add totals row
        $num_to_words = $pdf->convertCurrencyToWords($balance);
        $html .= '<tr style="background-color:#d9edf7; font-weight:bold;">
                    <td colspan="3" style="text-align:center; font-style:italic;"> ' . htmlspecialchars($num_to_words) . '</td>
                    <td style="width:13%;">' . number_format($totalDebit, 0) . '</td>
                    <td style="width:13%;">' . number_format($totalCredit, 0) . '</td>
                    <td style="width:17%;">' . number_format($balance, 0) . '</td>
                </tr>';
        
        $html .= '</table>';
        $pdf->writeHTML($html, true, false, true, false, '');
    
        // Filename and Output
        $filename = "general_ledger_of_{$lager_much_op_bal[0]['ac_name']}_from_{$formattedFromDate}_to_{$formattedToDate}.pdf";
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
    
        $response = [
            'lager_much_op_bal' => $lager_much_op_bal,
            'lager_much_all' => $lager_much_all,
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
                    <th style="width:13%; color:#17365D; font-weight:bold; text-align:center;">R/No</th>
                    <th style="width:12%; color:#17365D; font-weight:bold; text-align:center;">Date</th>
                    <th style="width:32%; color:#17365D; font-weight:bold; text-align:left; padding-left:10px;">Details</th>
                    <th style="width:13%; color:#17365D; font-weight:bold; text-align:right; padding-right:10px;">Debit</th>
                    <th style="width:13%; color:#17365D; font-weight:bold; text-align:right; padding-right:10px;">Credit</th>
                    <th style="width:17%; color:#17365D; font-weight:bold; text-align:right; padding-right:10px;">Balance</th>
                </tr>
            </thead>
            <tbody>';

        $html .= '
        <tr>
            <th style="width:13%;></th>
            <th style="width:12%;></th>
            <th  style="width:32%; text-align: center;font-weight:bold;">+----Opening Balance----+</th>
            <th style="width:13%></th>
            <th style="width:13%></th>
            <th style="width:17%; text-align:right;">' . number_format($opening_bal, 0) . '</th>
            
        </tr>';

        // Loop through data and append rows
        $count = 1;
        foreach ($lager_much_all as $items) {
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

            // Add row to table
            $html .= '<tr style="background-color:' . $bgColor . ';">
                <td>' . $items->prefix . $items->auto_lager . '</td>
                <td>' . Carbon::createFromFormat('Y-m-d', $items->jv_date)->format('d-m-y') . '</td>
                <td style="font-size: 10px; text-align:left; padding-left:10px;">' . $items->ac2 . ' ' . $items->Narration . '</td>
                <td style="text-align:right; padding-right:10px;">' . number_format($items->Debit, 0) . '</td>
                <td style="text-align:right; padding-right:10px;">' . number_format($items->Credit, 0) . '</td>
                <td style="text-align:right; padding-right:10px;">' . number_format($balance, 0) . '</td>
            </tr>';
            $count++;
        }

        // Add totals row
        $num_to_words = $pdf->convertCurrencyToWords($balance);
        $html .= '<tr style="background-color:#d9edf7; font-weight:bold;">
            <td colspan="3" style="text-align:center; font-style:italic;">' . htmlspecialchars($num_to_words) . '</td>
            <td style="text-align:right; padding-right:10px;">' . number_format($totalDebit, 0) . '</td>
            <td style="text-align:right; padding-right:10px;">' . number_format($totalCredit, 0) . '</td>
            <td style="text-align:right; padding-right:10px;">' . number_format($balance, 0) . '</td>
        </tr>';

        // Close tbody and table
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
            ->orderBy('jv_date','asc')
            ->orderBy('prefix','asc')
            ->orderBy('auto_lager','asc')
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
        $pdf->SetTitle('General Ledger R-' . $lager_much_op_bal[0]['ac_name']);
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
                        Account Name: <span style="color:black;">' . htmlspecialchars($lager_much_op_bal[0]['ac_name']) . '</span>
                    </td>
                    <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; padding:5px 10px; border-bottom:1px solid #000; border-left:1px solid #000; width:30%;">
                        Print Date: <span style="color:black;">' . htmlspecialchars($currentDate) . '</span>
                    </td>
                </tr>
                <tr>
                    <td style="font-size:12px; font-weight:bold; color:#17365D; padding:5px 10px; border-bottom:1px solid #000; width:70%;">
                        Address: <span style="color:black;">' . htmlspecialchars($lager_much_op_bal[0]['address']) . '</span>
                    </td>
                    <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; padding:5px 10px; border-bottom:1px solid #000; border-left:1px solid #000;width:30%;">
                        From Date: <span style="color:black;">' . htmlspecialchars($formattedFromDate) . '</span>
                    </td>
                </tr>
                <tr>
                    <td style="font-size:12px; font-weight:bold; color:#17365D; padding:5px 10px; border-bottom:1px solid #000; width:70%;">
                        Remarks: <span style="color:black;">' . htmlspecialchars($lager_much_op_bal[0]['remarks']) . '</span>
                    </td>
                    <td style="font-size:12px; font-weight:bold; color:#17365D; text-align:left; padding:5px 10px; border-bottom:1px solid #000; border-left:1px solid #000; width:30%;">
                        To Date: <span style="color:black;">' . htmlspecialchars($formattedToDate) . '</span>
                    </td>
                </tr>
            </table>';
        $pdf->writeHTML($html, true, false, true, false, '');
    
        // Table Headers
        $html = '<table border="1" style="border-collapse: collapse;text-align:center">
        <tr>
            <th style="width:13%;color:#17365D;font-weight:bold;">R/No</th>
            <th style="width:12%;color:#17365D;font-weight:bold;">Date</th>
            <th style="width:32%;color:#17365D;font-weight:bold;">Details</th>
            <th style="width:13%;color:#17365D;font-weight:bold;">Debit</th>
            <th style="width:13%;color:#17365D;font-weight:bold;">Credit</th>
            <th style="width:17%;color:#17365D;font-weight:bold;">Balance</th>
        </tr>
        <tr>
            <th></th>
            <th></th>
            <th  style="text-align: center;font-weight:bold;">+----Opening Balance----+</th>
            <th></th>
            <th></th>
            <th style="text-align: center">' . number_format($opening_bal, 0) . '</th>
        </tr>';
    
        $count = 1;
        foreach ($lager_much_all as $items) {
            $bgColor = ($count % 2 == 0) ? '#f1f1f1' : '#ffffff';
        
            // Update running balance
            if (!empty($items['Debit']) && is_numeric($items['Debit'])) {
                $balance += $items['Debit'];
                $totalDebit += $items['Debit'];
            }
        
            if (!empty($items['Credit']) && is_numeric($items['Credit'])) {
                $balance -= $items['Credit'];
                $totalCredit += $items['Credit'];
            }
        
            // Add row with merged Account Name & Remarks column
            $html .= '<tr style="background-color:' . $bgColor . ';">
                <td>' . $items['prefix'] . '' . $items['auto_lager'] . '</td>
                <td>' . Carbon::createFromFormat('Y-m-d', $items['jv_date'])->format('d-m-y') . '</td>
                <td style="font-size: 10px;">' . $items['ac2'] . ' ' . $items['Narration'] . '</td>
                <td>' . number_format($items['Debit'], 0) . '</td>
                <td>' . number_format($items['Credit'], 0) . '</td>
                <td>' . number_format($balance, 0) . '</td>
            </tr>';
            $count++;
        }
    
        // Add totals row
        $num_to_words = $pdf->convertCurrencyToWords($balance);
        $html .= '<tr style="background-color:#d9edf7; font-weight:bold;">
                    <td colspan="3" style="text-align:center; font-style:italic;"> ' . htmlspecialchars($num_to_words) . '</td>
                    <td style="width:13%;">' . number_format($totalDebit, 0) . '</td>
                    <td style="width:13%;">' . number_format($totalCredit, 0) . '</td>
                    <td style="width:17%;">' . number_format($balance, 0) . '</td>
                </tr>';
        
        $html .= '</table>';
        $pdf->writeHTML($html, true, false, true, false, '');

        // Filename and Output
        $filename = "general_ledger_r_of_{$lager_much_op_bal[0]['ac_name']}_from_{$formattedFromDate}_to_{$formattedToDate}.pdf";
        $pdf->Output($filename, 'D');
    }
    
}
