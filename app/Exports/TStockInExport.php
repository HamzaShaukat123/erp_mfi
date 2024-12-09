<?php

namespace App\Exports;

use App\Models\gd_pipe_pur_by_item_name;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TStockInExport implements FromCollection, WithHeadings
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
            'Purchase ID',
            'Prefix',
            'Group Name',
            'Purchase Date',
            'Purchase Remarks',
            'Purchase Bill No.',
            'Mill Gate No.',
            'Item Code',
            'Purchase Quantity',
            'Company Name',
        ];
    }
}
