<?php

namespace App\Exports;

use App\Models\pipe_sale_by_account;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class Sale2Export implements FromCollection, WithHeadings
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
            'Sale Inv',
            'Remarks',
            'cr amount',
            'Cash Pur address',
        ];
    }
}
