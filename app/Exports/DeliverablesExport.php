<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class DeliverablesExport implements FromView
{
    protected $records;
    protected $columns;

    public function __construct($records, $columns)
    {
        $this->records = $records;
        $this->columns = $columns;
    }

    public function view(): View
    {
        return view('exports.deliverables', [
            'records' => $this->records,
            'columns' => $this->columns,
        ]);
    }
}
