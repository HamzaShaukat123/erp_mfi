<?php

namespace App\Exports;

use App\Models\gd_pipe_pur_by_item_group;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GoDownByItemGrpSIExport implements FromCollection, WithHeadings
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
            'Ac1',
            'Ac2',
            'Date',
            'NO',
            'remarks',
            'cr amount',
            'saler address',
            'pur_bill_no',
            'sal_inv',
        ];
    }
}
