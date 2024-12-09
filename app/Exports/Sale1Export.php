<?php

namespace App\Exports;

use App\Models\sale_by_account;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class Sale1Export implements FromCollection, WithHeadings
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
            'Account Code',
            'Name/Address',
            'Date',
            'Bill',
            'Sale Inv',
            'Remarks',
            'Net Bill Amount',
        ];
    }
}
