<?php

namespace App\Exports;

use App\Invoice;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ServicesProvidedExport implements FromView
{
    /**
     * Class constructor.
     */
    public function __construct($services)
    {
        $this->services = $services;
    }

    public function view(): View
    {
        return view('exports.services-provided', ['services' => $this->services]);
    }
}