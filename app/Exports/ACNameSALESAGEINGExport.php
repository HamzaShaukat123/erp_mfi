<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class ACNameSALESAGEINGExport implements FromArray
{
    protected $salesData;

    public function __construct(array $salesData)
    {
        $this->salesData = $salesData;
    }

    public function array(): array
    {
        return $this->salesData;
    }
}
