<?php

namespace App\Http\Controllers;

use App\Models\Estimate;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

class EstimatePdfController extends Controller
{
    public function __invoke(Estimate $estimate)
    {
        $estimate->load(['customer', 'items']);

        $pdf = Pdf::loadView('pdf.estimate', [
            'estimate' => $estimate,
            'settings' => Setting::current(),
        ]);

        return $pdf->stream($estimate->number.'.pdf');
    }
}
