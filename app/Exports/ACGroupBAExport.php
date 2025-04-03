<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class ACGroupBAExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $exportData = [];

        foreach ($this->data as $head => $subheads) {
            // Head Title Row
            $exportData[] = [$head, '', '', '', '', ''];

            foreach ($subheads->groupBy('subhead') as $subHead => $items) {
                // Subhead Title Row
                $exportData[] = [$subHead, '', '', '', '', ''];
                // Column Headings Row
                $exportData[] = ['S/No', 'AC', 'Account Name', 'Address', 'Debit', 'Credit'];

                $rowCount = 1;
                $subTotalDebit = 0;
                $subTotalCredit = 0;

                foreach ($items as $item) {
                    $exportData[] = [
                        $rowCount++,
                        $item->ac_code,
                        $item->ac_name,
                        $item->address . ' ' . $item->phone,
                        $item->Debit,
                        $item->Credit
                    ];

                    $subTotalDebit += $item->Debit;
                    $subTotalCredit += $item->Credit;
                }

                // Subtotal Row
                $exportData[] = ['', '', 'Sub Total', '', $subTotalDebit, $subTotalCredit];
                // Balance Row (if applicable)
                $exportData[] = ['', '', 'Balance', '', $subTotalDebit - $subTotalCredit, ''];
            }
        }

        return new Collection($exportData);
    }

    public function headings(): array
    {
        return ['S/No', 'AC', 'Account Name', 'Address', 'Debit', 'Credit'];
    }
}
