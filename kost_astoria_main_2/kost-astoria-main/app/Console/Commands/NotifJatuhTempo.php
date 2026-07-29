<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class NotifJatuhTempo extends Command
{
    protected $signature = 'notif:jatuh-tempo';
    protected $description = 'Kirim notifikasi WA jika sewa hampir jatuh tempo';

    public function handle()
    {
        $transactions = Transaction::with('user')
            ->where('status', '!=', 'Selesai')
            ->get()
            ->filter(function ($trx) {
                $endDate = Carbon::createFromFormat('d-m-Y', $trx->end_date_sewa);
                return $endDate->isSameDay(Carbon::now()->addDays(3));
            });

        foreach ($transactions as $trx) {
            $user = $trx->user;
            if (!$user || !$user->no_wa) continue;

            $number = preg_replace('/[^0-9]/', '', $user->no_wa);
            if (substr($number, 0, 1) == '0') {
                $number = '62' . substr($number, 1);
            }

            if (strlen($number) < 10 || strlen($number) > 15) continue;

            $apiKey = 'rnkameBYtqISXMFzgpayplUfijv6mm';
            $sender = '6288983864440';
            $message = "Hai {$user->name}, masa sewa kamar kamu akan *berakhir pada {$trx->end_date_sewa}*. "
                     . "Segera hubungi admin jika ingin memperpanjang ya!\n\n"
                     . "Kost Astoria";

            Http::get('https://seender.biz.id/send-message', [
                'api_key' => $apiKey,
                'sender' => $sender,
                'number' => $number,
                'message' => $message
            ]);
        }

        $this->info("Notifikasi jatuh tempo terkirim.");
    }
    
}
