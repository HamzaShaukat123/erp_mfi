<?php

namespace App\Exports;

use App\Models\activite11_sales_pipe;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DailyRegSale2Export implements FromCollection, WithHeadings
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
            'Prefix',
            'Account ID',
            'Bill Amt',
            'Bill Discount',
            'Sales Remarks',
            'Pur Ord No.',
            'Company ID',
            'Cash Name',
            'Convance Charges',
            'Labour Charges',
            'Account Name',
            'Company Name',
        ];
    }
}
