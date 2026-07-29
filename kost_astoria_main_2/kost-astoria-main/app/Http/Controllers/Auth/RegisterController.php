<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\{User,DataUser};
use ErrorException;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http; 
use Spatie\Permission\Models\Role;
use DB;
class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:50'],
            'no_wa' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'max:50', 'unique:users'],
            'role' => ['required'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'foto_ktp' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ],
        [
            "name.required"      => 'Nama Tidak Boleh kosong.',
            "name.max"           => 'Nama Tidak Boleh Lebih Dari 50 Karakter.',
            "no_wa.required"     => 'Nomor WA Tidak Boleh Kosong.',
            "no_wa.unique"       => 'Nomor WA Sudah Terdaftar.',
            "email.required"     => 'Email Tidak Boleh Kosong.',
            "email.max"          => 'Email Tidak Boleh Lebih Dari 50 Karakter',
            "email.unique"       => 'Email Sudah Terdaftar.',
            "role.required"      => 'Jenis Pendaftaran Harus Di Pilih.',
            "password.required"  => 'Password Tidak Boleh Kosong.',
            "password.min"       => 'Password Minimal 8 Karakter.',
            "password.confirmed" => 'Password Tidak Sama.',
            "foto.image"         => 'File Harus Berupa Gambar.',
            "foto.mimes"         => 'Format Gambar Harus jpeg,png,jpg.',
            "foto.max"           => 'Ukuran Gambar Maksimal 2MB.',
            "foto_ktp.image"     => 'File KTP Harus Berupa Gambar.',
            "foto_ktp.mimes"     => 'Format Gambar KTP Harus jpeg,png,jpg.',
            "foto_ktp.max"       => 'Ukuran Gambar KTP Maksimal 2MB.',
        ]);
    }

protected function create(array $data)
{
    try {
        DB::beginTransaction();

        $foto = null;
        if (isset($data['foto'])) {
            $foto = 'user_foto_' . time() . '.' . $data['foto']->getClientOriginalExtension();
            $data['foto']->storeAs('public/images/foto_profile', $foto);
        }

        $foto_ktp = null;
        if (isset($data['foto_ktp'])) {
            $foto_ktp = 'user_ktp_' . time() . '.' . $data['foto_ktp']->getClientOriginalExtension();
            $data['foto_ktp']->storeAs('public/images/foto_ktp', $foto_ktp);
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'no_wa' => $data['no_wa'],
            'role' => $data['role'],
            'password' => Hash::make($data['password']),
            'foto' => $foto,
            'foto_ktp' => $foto_ktp,
        ]);

        $user->assignRole($data['role']);
        $apiKey = "rnkameBYtqISXMFzgpayplUfijv6mm";
        $sender = "6288983864440"; 
        $number = $user->no_wa;
        if (substr($number, 0, 1) === '0') {
            $number = '62' . substr($number, 1);
        }

        $message = "Selamat datang di Kost Astoria\n\nHai {$user->name}, akun kamu berhasil didaftarkan!\nSekarang kamu bisa cari kost lebih mudah";

        Http::get('https://seender.biz.id/send-message', [
            'api_key' => $apiKey,
            'sender' => $sender,
            'number' => $number,
            'message' => $message,
        ]);

        DB::commit();
        return $user;

    } catch (ErrorException $e) {
        DB::rollback();
        throw new ErrorException($e->getMessage());
    }
}
}
