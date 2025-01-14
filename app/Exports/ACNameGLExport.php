<?php

namespace App\Exports;

use App\Models\AC;
use App\Models\lager_much_op_bal;
use App\Models\lager_much_all;
use Maatwebsite\Excel\Concerns\FromCollection;

class ACNameGLExport implements FromCollection
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
