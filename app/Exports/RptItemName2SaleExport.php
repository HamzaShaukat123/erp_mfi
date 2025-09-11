<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RptItemName2SaleExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;
    protected $count = 1; // for serial no

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function map($row): array
    {
        return [
            $this->count++,                                    // S/No
            \Carbon\Carbon::parse($row->sa_date)->format('d-m-y'), // Date
            $row->prefix . $row->Sal_inv_no,                   // Inv ID
            $row->ac_name,                                     // Account Name
            $row->qty,                                         // Qty
            $row->price,                                       // Price
            $row->length,                                      // Len
            $row->percent,                                     // %
            $row->weight,                                      // Weight
        ];
    }

    public function headings(): array
    {
        return [
            'S/No',
            'Date',
            'Inv ID',
            'Account Name',
            'Qty',
            'Price',
            'Len',
            '%',
            'Weight',
        ];
    }
}
