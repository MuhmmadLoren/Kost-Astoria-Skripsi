<?php

namespace App\Http\Controllers\Owner;

use ErrorException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Owner\PaymentService;
use App\Models\payment;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{
    protected $payment;

    public function __construct(PaymentService $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Get all payment
     * 
     * @return mixed
     */
    public function payment()
    {
        try {
            return $this->payment->getAllPayment();
        } catch (ErrorException $e) {
            throw new ErrorException($e->getMessage());
        }
    }
    public function exportPDF()
{
    $payment = Payment::all();
    $pdf = Pdf::loadView('pemilik.laporan.pdf', compact('payment'))->setPaper('a4', 'landscape');
    return $pdf->download('laporan-pembayaran.pdf');
}
}
