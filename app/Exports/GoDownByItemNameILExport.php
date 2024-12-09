<?php

namespace App\Exports;

use App\Models\gd_pipe_item_ledger5_opp;
use App\Models\gd_pipe_item_ledger;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GoDownByItemNameILExport implements FromCollection, WithHeadings
{
    protected $data;
    protected $op_qty;

    public function __construct($data, $op_qty)
    {
        $this->data = $data;
        $this->op_qty = $op_qty;
    }

    public function collection()
    {
        $balance = $this->op_qty; // Start with the opening quantity

        foreach ($this->data as $item) {
            // Calculate the current balance
            $add = !empty($item['add_qty']) ? $item['add_qty'] : 0;
            $less = !empty($item['less']) ? $item['less'] : 0;
            $balance += $add - $less; // Update balance

            // Prepare the row with the calculated balance
            $result[] = [
                'ID' => $item['Sal_inv_no'],
                'Date' => \Carbon\Carbon::parse($item['sa_date'])->format('d-m-Y'),
                'Entry Of' => $item['entry_of'],
                'Account Name' => $item['ac_name'],
                'Remarks' => $item['Sales_Remarks'],
                'Add' => $add,
                'Less' => $less,
                'Balance' => $balance, // Add calculated balance
            ];
        }

        return collect($result); // Return as a collection
    }

    public function headings(): array
    {
        return [
            'ID',
            'Date',
            'Entry Of',
            'Account Name',
            'Remarks',
            'Add',
            'Less',
            'Balance (Opening Bal:'.$this->op_qty.')',
        ];
    }
}
