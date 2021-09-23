<?php

namespace App\Exports;

use App\Invoice;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ConsultationsExport implements FromView
{
    /**
     * Class constructor.
     */
    public function __construct($clinicHistories)
    {
        $this->clinicHistories = $clinicHistories;
    }

    public function view(): View
    {
        return view('exports.consultations', ['clinicHistories' => $this->clinicHistories]);
    }
}