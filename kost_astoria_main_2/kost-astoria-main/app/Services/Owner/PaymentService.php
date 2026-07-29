<?php

namespace App\Services\Owner;

use ErrorException;
use App\Models\Payment;
use Illuminate\Support\Facades\Session;

class PaymentService {

    // Mengambil semua data payment (hanya data)
    public function getAllPaymentData()
    {
        return Payment::all();
    }

    // Menampilkan view dengan semua data payment
    public function getAllPayment()
    {
        $payment = $this->getAllPaymentData();
        return view('pemilik.laporan.index', compact('payment'));
    }

    // Mengambil data payment berdasarkan ID
    public function getPaymentById($id)
    {
        try {
            $payment = Payment::findOrFail($id);
            return $payment;
        } catch (ErrorException $e) {
            throw new ErrorException($e->getMessage());
        }
    }

    // Menampilkan halaman index users
    public function index()
    {
        try {
            $payment = $this->getAllPaymentData();

            if ($payment->isEmpty()) {
                Session::flash('error', 'Data Payment Kosong');
                return redirect('/home');
            }

            return view('pemilik.laporan.index', compact('payment'));

        } catch (ErrorException $e) {
            Session::flash('error', $e->getMessage());
            return redirect('/home');
        }
    }
}
