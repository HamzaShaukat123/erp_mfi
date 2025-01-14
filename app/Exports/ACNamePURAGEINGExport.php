<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class ACNamePURAGEINGExport implements FromArray
{
    protected $purchaseData;

    public function __construct(array $purchaseData)
    {
        $this->purchaseData = $purchaseData;
    }

    public function array(): array
    {
        return $this->purchaseData;
    }
}
