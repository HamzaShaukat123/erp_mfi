<?php

namespace App\Exports;

use App\Models\lager_much_op_bal;
use Maatwebsite\Excel\Concerns\FromCollection;

class ACNameGLExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return lager_much_op_bal::all();
    }
}
