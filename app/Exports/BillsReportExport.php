<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class BillsReportExport implements FromView
{
    /**
     * Class constructor.
     */
    public function __construct($bills)
    {
        $this->bills = $bills;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        return view('exports.bills-report', ['bills' => $this->bills]);
    }
}
