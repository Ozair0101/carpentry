<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoicePdfController extends Controller
{
    public function __invoke(Invoice $invoice)
    {
        $invoice->load(['customer', 'items', 'payments']);

        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice,
            'settings' => Setting::current(),
        ]);

        return $pdf->stream($invoice->number.'.pdf');
    }
}
