<?php

namespace App\Exports;

use App\Models\gd_pipe_addless_by_item_name;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TStockBalExport implements FromCollection, WithHeadings
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
            'Sal Inv No',
            'Item Code',
            'PCs Add',
            'PCs Less',
            'Remarks',
            'Reason',
            'Sa Date',
        ];
    }
}
