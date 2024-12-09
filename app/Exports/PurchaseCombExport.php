<?php

namespace App\Exports;

use App\Models\both_pur_rpt_by_account;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PurchaseCombExport implements FromCollection, WithHeadings
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
            'Entry Of',
            'Ac1',
            'Ac2',
            'Date',
            'NO',
            'remarks',
            'dr amount',
            'cr amount',
        ];
    }
}
