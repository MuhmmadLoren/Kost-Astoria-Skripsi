<?php

namespace App\Http\Controllers;
use Carbon\carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\{Transaction,payment,kamar,SimpanKamar};
use PDF;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $auth = Auth::user()->role;

        if (Auth::check()) {
            if ($auth == 'Admin') {
                $aktif = Transaction::where('status','Proses')->count(); // Penghuni Aktif
                $total = Transaction::whereIn('status',['Proses','Done'])->count(); // Total Penghuni

                $stok_kamar = kamar::sum('stok_kamar');
                $sisa_kamar = kamar::sum('sisa_kamar');
                $favorite = SimpanKamar::count();
                return view('admin.index', compact('aktif','total','stok_kamar','sisa_kamar','favorite'));
            }
            elseif (Auth::user()->role == 'Pemilik') {
                $aktif = Transaction::where('pemilik_id',Auth::id())->where('status','Proses')->count(); // Penghuni Aktif
                $total = Transaction::where('pemilik_id',Auth::id())->whereIn('status',['Proses','Done'])->count(); // Total Penghuni

                $pendapatan = payment::with(['transaksi' => function($a) {
                    $a->where('pemilik_id',Auth::id());
                }])
                ->sum('jumlah_bayar');

                $pendapatanMonth = payment::with(['transaksi' => function($a) {
                    $a->where('pemilik_id',Auth::id());
                }])
                ->whereMonth('updated_at',Carbon::now()->format('m'))
                ->whereYear('updated_at',Carbon::now()->format('Y'))
                ->sum('jumlah_bayar');

                $pendapatanYear = payment::with(['transaksi' => function($a) {
                    $a->where('pemilik_id',Auth::id());
                }])
                ->whereYear('updated_at',Carbon::now()->format('Y'))
                ->sum('jumlah_bayar');

                $pendapatanPrevYear = payment::with(['transaksi' => function($a) {
                    $a->where('pemilik_id',Auth::id());
                }])
                ->whereYear('updated_at',date("Y",strtotime("-1 year")))
                ->sum('jumlah_bayar');

                $jenis_kamar = kamar::where('user_id',Auth::id())->count();

                $stok_kamar = kamar::where('user_id',Auth::id())->sum('stok_kamar');
                $sisa_kamar = kamar::where('user_id',Auth::id())->sum('sisa_kamar');
                $favorite = SimpanKamar::with(['kamar' => function($x) {
                    $x->where('user_id',Auth::id());
                }])
                ->count();

                return view('pemilik.index', \compact('aktif','total','pendapatan','pendapatanMonth','pendapatanYear','pendapatanPrevYear','jenis_kamar','stok_kamar','sisa_kamar','favorite'));
            } elseif(Auth::user()->role == 'Pencari') {
                $aktif = Transaction::where('user_id',Auth::id())->where('status','Proses')->count();
                return view('user.index', \compact('aktif'));
            } else {
                abort(404);
            }
        }
    }
public function exportPendapatanPDF($periode)
{
    $auth = Auth::user();
    if ($auth->role !== 'Pemilik') abort(403);

    $label = '';
    $jumlah = null;
    $dataHarian = null;
    $dataBulanan = null;

    if ($periode === 'harian') {
        $label = 'Hari Ini (' . Carbon::today()->translatedFormat('d F Y') . ')';

        $jumlah = payment::with(['transaksi' => function ($q) use ($auth) {
            $q->where('pemilik_id', $auth->id);
        }])
        ->whereDate('updated_at', Carbon::today())
        ->sum('jumlah_bayar');

    } elseif ($periode === 'bulanan') {
        $label = 'Bulan ' . now()->translatedFormat('F Y');
        $daysInMonth = now()->daysInMonth;
        $dataHarian = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $tanggal = now()->copy()->startOfMonth()->day($day);

            $total = payment::with(['transaksi' => function ($q) use ($auth) {
                $q->where('pemilik_id', $auth->id);
            }])
            ->whereDate('updated_at', $tanggal)
            ->sum('jumlah_bayar');

            if ($total > 0) {
                $dataHarian[] = [
                    'tanggal' => $tanggal->translatedFormat('d F Y'),
                    'jumlah' => $total
                ];
            }
        }

    } elseif ($periode === 'tahunan') {
        $label = 'Tahun ' . now()->year;
        $dataBulanan = [];

        for ($month = 1; $month <= 12; $month++) {
            $total = payment::with(['transaksi' => function ($q) use ($auth) {
                $q->where('pemilik_id', $auth->id);
            }])
            ->whereMonth('updated_at', $month)
            ->whereYear('updated_at', now()->year)
            ->sum('jumlah_bayar');

            if ($total > 0) {
                $dataBulanan[] = [
                    'bulan' => Carbon::create()->month($month)->translatedFormat('F'),
                    'jumlah' => $total
                ];
            }
        }

    } else {
        abort(404, 'Periode tidak dikenali');
    }

    $pdf = PDF::loadView('pemilik.laporan.laporan-keuangan', [
        'periode' => ucfirst($periode),
        'label' => $label,
        'jumlah' => $jumlah,
        'dataHarian' => $dataHarian,
        'dataBulanan' => $dataBulanan,
    ]);

    return $pdf->download("pendapatan_{$periode}_" . now()->format('Ymd') . ".pdf");
}
}
