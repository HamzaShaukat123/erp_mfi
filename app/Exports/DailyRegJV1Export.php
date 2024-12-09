<?php

namespace App\Exports;

use App\Models\activites10_gen_ac;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DailyRegJV1Export implements FromCollection, WithHeadings
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
            'R/No',
            'Date',
            'Remarks',
            'Amount',
            'Acc Debit ID',
            'Acc Credit ID',
            'Acc Debit Name',
            'Acc Credit Name',        
        ];
    }
}
