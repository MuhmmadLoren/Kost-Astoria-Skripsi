<?php

namespace App\Http\Controllers\Frontend;

use Carbon\carbon;
use App\Models\kamar;
use App\Models\Promo;
use App\Models\SimpanKamar;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FrontendsController extends Controller
{
    // Homepage
    public function homepage(Request $request)
    {
        $cari = $request->cari;
        $kamar = kamar::with('promo')
            ->when(isset($cari), function ($a) use ($cari) {
                $a->orwhere('nama_kamar', 'like', "%" . $cari . "%");
            })
            ->where('status', 1)
            ->where('is_active', 1)
            ->orderBy('created_at', 'DESC')
            ->paginate(8);
        $promo = Promo::whereHas('kamar', function ($a) {
            $a->where('status', 1)->where('is_active', 1);
        })->where('end_date_promo', '>=', carbon::now()->format('d F, Y'))->get();
        return view('front.index', \compact('kamar', 'promo'));
    }

    // Show Kamar
    public function showkamar($slug)
    {
        $kamar = kamar::with(['promo' => function ($q) {
                $q->where('status', '1');
            }, 'alamat'])
            ->where('slug', $slug)->first();
        $fav = SimpanKamar::where('kamar_id', $kamar->id)->where('user_id', Auth::id())->first();
        $relatedKos = kamar::with(['promo' => function ($a) {
            $a->where('end_date_promo', '>=', carbon::now()->format('d F, Y'));
        }, 'alamat'])->whereNotIn('slug', [$slug])
            ->where('status', 1)
            ->where('is_active', 1)
            ->limit(4)->get();

        return view('front.show', compact('kamar', 'relatedKos', 'fav'));
    }

    // Show semua kamar
    public function showAllKamar(Request $request)
    {
        $cari = $request->cari;
        $allKamar = kamar::with(['promo' => function ($a) {
            $a->where('end_date_promo', '>=', carbon::now()->format('d F, Y'));
        }])
            ->when(isset($cari), function ($a) use ($cari) {
                $a->where('nama_kamar', 'like', "%" . $cari . "%")
                    ->orWhereHas('favorite', function ($q) use ($cari) {
                        $q->where('user_id', 'like', "%" . $cari . "%")
                            ->where('user_id', Auth::id());
                    });
            })
            ->where('status', 1)
            ->where('is_active', 1)
            ->orderBy('created_at', 'DESC')
            ->paginate(12);

        $provinsi = Kamar::with(['promo' => function ($a) {
                $a->where('end_date_promo', '>=', carbon::now()->format('d F, Y'));
            }])
            ->where('status', 1)
            ->where('is_active', 1)
            ->get();
        $select = [];
        $select['jenis_kamar'] = $request->jenis_kamar;
        $select['name']        = $request->nama_provinsi;
        $select['user_id']     = $request->user;
        return view('front.allCardContent', \compact('allKamar', 'select', 'provinsi', 'cari'));
    }

    // Filter kamar
    public function filterKamar(Request $request)
    {
        $allKamar = kamar::with([
            'promo' => function ($a) {
                $a->where('end_date_promo', '>=', carbon::now()->format('d F, Y'));
            }
        ])
        ->when($request->jenis_kamar != 'all', function($query) use ($request) {
            $query->where('jenis_kamar', $request->jenis_kamar);
        })
        ->where('status', 1)
        ->where('is_active', 1)
        ->orderBy('created_at', 'DESC')
        ->paginate(12);


        $select = [];
        $select['jenis_kamar'] = $request->jenis_kamar;
        return view('front.allCardContent', \compact('allKamar', 'select'));
    }

    // Show by kota
    public function showByKota(Request $request)
    {
        $kota = $request->kota;
        $kamar = kamar::with('promo')
            ->where('status', 1)
            ->where('is_active', 1)
            ->orderBy('created_at', 'DESC')
            ->paginate(12);
        return view('front.showByKota', \compact('kamar', 'kota'));
    }

    // Simpan kamar
    public function simpanKamar(Request $request)
    {
        $simpan = new SimpanKamar;
        $simpan->user_id  = Auth::id();
        $simpan->kamar_id = $request->id;
        $simpan->save();

        return back();
    }

    // Hapus kamar disimpan
    public function hapusKamar(Request $request)
    {
        $hapus = SimpanKamar::find($request->id);
        $hapus->delete();

        return back();
    }
}
