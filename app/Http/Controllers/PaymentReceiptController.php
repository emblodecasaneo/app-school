<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentReceiptController extends Controller
{
    /**
     * Affiche le reçu de paiement pour impression
     */
    public function show($id)
    {
        $payment = Payment::with(['student', 'classe', 'schoolyear'])->findOrFail($id);
        
        return view('paiements.receipt', compact('payment'));
    }

    /**
     * Génère un PDF du reçu de paiement
     */
    public function generatePdf($id)
    {
        $payment = Payment::with(['student', 'classe', 'schoolyear'])->findOrFail($id);
        
        $pdf = PDF::loadView('paiements.receipt-pdf', compact('payment'));
        
        return $pdf->stream('recu-paiement-' . $payment->id . '.pdf');
    }
}
