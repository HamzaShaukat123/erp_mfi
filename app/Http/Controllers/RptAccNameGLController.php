<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AC;
use App\Models\lager_much_op_bal;
use App\Models\lager_much_all;
use App\Exports\ACNameGLExport;
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
        $pdf->setTableHtml($html);
    
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
