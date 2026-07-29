<?php

namespace App\Http\Controllers\User;

use Carbon\carbon;
use ErrorException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\TransactionRequest;
use App\Http\Requests\KonfirmasiPembayaranRequest;
use App\Models\{Transaction,kamar,payment,User,Bank};

class TransactionController extends Controller
{
    // Tagihan
    public function tagihan()
    {
        try {
            $tagihan = Transaction::where('user_id', Auth::id())->get();
            return view('user.payment.index', compact('tagihan'));
        } catch (ErrorException $e) {
            throw new ErrorException($e->getMessage());
        }
    }

    // Transaction Sewa Kamar
    public function store(TransactionRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $room = kamar::with('promo')->where('id', $id)->first();

            if ($room->is_active == 0 || $room->status == 0) {
                Session::flash('error', 'Pemesanan kamar gagal, kamar sedang tidak aktif !');
                return back();
            } elseif ($room->sisa_kamar <= 0) {
                Session::flash('error', 'Kamar Penuh !');
                return back();
            }

            $iduser = Auth::id();
            $number = mt_rand(100, 999);
            $date = date('dmy');
            $key = Str::random(9999);

            $kamar = new Transaction;
            $kamar->key = 'confirm-payment-' . $key;
            $kamar->transaction_number = 'BOOK-' . $number . $id . '-' . $date;
            $kamar->kamar_id = $room->id;
            $kamar->user_id = $iduser;
            $kamar->pemilik_id = $room->user_id;
            $kamar->lama_sewa = $request->lama_sewa;
            $kamar->hari = $request->lama_sewa == 3 ? 90 : 30;

            $points = calculatePointUser($iduser);
            $hargaPromo = $room->promo && $room->promo->status == '1' && $room->promo->end_date_promo >= carbon::now()->format('d F, Y')
                ? $room->promo->harga_promo
                : $room->harga_kamar;

            $kamar->harga_kamar = $hargaPromo;

            $hargaTotal = $hargaPromo * $request->lama_sewa;

            if ($request->credit) {
                $kamar->harga_total = ($hargaTotal + $number) - $points;
            } else {
                $kamar->harga_total = $hargaTotal + $number;
            }

            $kamar->tgl_sewa = Carbon::parse($request->tgl_sewa)->format('d-m-Y');
            $kamar->end_date_sewa = Carbon::parse($request->tgl_sewa)->addDays($kamar->hari)->format('d-m-Y');
            $kamar->save();

            if ($kamar) {
                $payment = new payment;
                $payment->transaction_id = $kamar->id;
                $payment->user_id = $iduser;
                $payment->kamar_id = $id;
                $payment->save();
            }

            if ($request->credit) {
                $point = User::findOrFail($iduser);
                $point->credit = 0;
                $point->save();
            }

            DB::commit();

            // ✅ Kirim Notifikasi WhatsApp
            $user = Auth::user();
            $apiKey = "rnkameBYtqISXMFzgpayplUfijv6mm";
            $sender = "6288983864440";
            $number = $user->no_wa;
            if (substr($number, 0, 1) === '0') {
                $number = '62' . substr($number, 1);
            }

            $message = "📢 Booking berhasil!\n\nHai {$user->name}, kamu telah booking kamar *{$room->nama_kamar}*.\n" .
                "Silakan segera lakukan pembayaran sebesar *Rp " . number_format($kamar->harga_total, 0, ',', '.') . "*.\n\n" .
                "🧾 Tagihanmu: https://kost_astoria.com/user/tagihan";

            Http::get('https://seender.biz.id/send-message', [
                'api_key' => $apiKey,
                'sender' => $sender,
                'number' => $number,
                'message' => $message,
            ]);

            Session::flash('success', 'Berhasil, Silahkan Melakukan Pembayaran');
            return redirect('/user/tagihan');

        } catch (ErrorException $e) {
            DB::rollback();
            throw new ErrorException($e->getMessage());
        }
    }

    // Detail Pembayaran
    public function detail_payment($key)
    {
        try {
            $transaksi = Transaction::where('key', $key)->first();
            $bank = Bank::all();
            if ($transaksi->payment->status == 'Pending') {
                return view('user.payment.show', compact('transaksi', 'bank'));
            } else {
                Session::flash('error', 'Pembayaran Sudah Terkirim');
                return redirect('/user/tagihan');
            }
        } catch (ErrorException $e) {
            throw new ErrorException($e->getMessage());
        }
    }

    // konfirmasi pembayaran kamar
    public function update(KonfirmasiPembayaranRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $konfirmasi = Transaction::findOrFail($id);
            $konfirmasi->update(['status' => 'Pending']);

            if ($konfirmasi) {
                $foto_bukti = $request->file('bukti_bayar');
                $foto_selfie = $request->file('foto_selfie');
                $bukti_bayar = time() . "_" . $foto_bukti->getClientOriginalName();
                $selfie = time() . "_" . $foto_selfie->getClientOriginalName();

                $foto_bukti->storeAs('public/images/bukti_bayar', $bukti_bayar);
                $foto_selfie->storeAs('public/images/bukti_selfie', $selfie);

                $payment = payment::where('transaction_id', $id)->first();
                $payment->type_transfer = 'BANK';
                $payment->nama_bank = $request->nama_bank;
                $payment->nama_pemilik = $request->nama_pemilik;
                $payment->bank_tujuan = $request->bank_tujuan;
                $payment->status = 'Success';
                $payment->jumlah_bayar = $konfirmasi->harga_total;
                $payment->tgl_transfer = $request->tgl_transfer;
                $payment->bukti_bayar = $bukti_bayar;
                $payment->foto_selfie = $selfie;
                $payment->save();
            }

            DB::commit();
            Session::flash('success', 'Pembayaran Terkirim');
            return redirect('/user/tagihan');
        } catch (ErrorException $e) {
            DB::rollback();
            throw new ErrorException($e->getMessage());
        }
    }
}
