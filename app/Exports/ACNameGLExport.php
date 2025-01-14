<?php

namespace App\Exports;

use App\Models\AC;
use App\Models\lager_much_op_bal;
use App\Models\lager_much_all;
use Maatwebsite\Excel\Concerns\FromCollection;

class ACNameGLExport implements FromCollection
{
    protected $rows;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function headings(): array
    {
        // Custom heading or leave empty to use the first row of the array as headings
        return [];
    }
}
