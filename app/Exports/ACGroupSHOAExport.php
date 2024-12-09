<?php

namespace App\Exports;

use App\Models\balance_sub_head;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ACGroupSHOAExport implements FromCollection, WithHeadings
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
            'AC Code',
            'AC Name',
            'Address',
            'Debit',
            'Credit',  
        ];
    }
}
