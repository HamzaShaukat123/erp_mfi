<?php

namespace App\Exports;

use App\Models\gd_pipe_sale_by_item_name;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TStockOutExport implements FromCollection, WithHeadings
{
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
            'Sal Inv No.',
            'Prefix',
            'Purchase Inv',
            'Mill Gate',
            'Item Code',
            'Sales Qty',
            'Remarks',
            'Weight Pc',
            'Account Name',
            'Sa Date',
            'Company Name',
        ];
    }
}
