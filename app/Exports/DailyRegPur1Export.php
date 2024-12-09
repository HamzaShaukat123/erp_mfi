<?php

namespace App\Exports;

use App\Models\activite7_pur;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DailyRegPur1Export implements FromCollection, WithHeadings
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
            'Pur ID',
            'Account Cod',
            'Bill Amt',
            'Bill Discount',
            'Pur Remarks',
            'Pur Bill No.',
            'Sales Against',
            'Cash Saler Name',
            'Convance Charges',
            'Labour Charges',
            'Account Name',
        ];
    }
}
