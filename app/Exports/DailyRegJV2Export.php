<?php

namespace App\Exports;

use App\Models\activites9_gen_acas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DailyRegJV2Export implements FromCollection, WithHeadings
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
            'JV No.',
            'Date',
            'Account Name',
            'Debit Amount',
            'Credit Amount',
            'Remarks',
            'Narration',        
        ];
    }
}
