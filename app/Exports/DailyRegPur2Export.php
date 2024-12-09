<?php

namespace App\Exports;

use App\Models\activite13_pur_pipe;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DailyRegPur2Export implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Sal Inv No',
            'Account ID',
            'Bill Amt',
            'Bill Discount',
            'Sales Remarks',
            'Pur Ord No.',
            'Cash Pur Acc',
            'Convance Charges',
            'Labour Charges',
            'Account Name',
            'Customer Name',
        ];
    }
}
