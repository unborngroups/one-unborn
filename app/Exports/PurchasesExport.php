<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class PurchasesExport implements FromCollection
{
    protected $records;

    public function __construct($records)
    {
        $this->records = $records;
    }

    public function collection()
    {
        return collect($this->records);
    }
}
