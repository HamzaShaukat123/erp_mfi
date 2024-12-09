<?php

namespace App\Exports;

use App\Models\all_payments_by_party;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VouchersExport implements FromCollection, WithHeadings
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
            'Account Code',
            'Ac2',
            'JV Date',
            'Auto Ledger',
            'Narration',
            'Debit',
            'Credit',
        ];
    }
}
