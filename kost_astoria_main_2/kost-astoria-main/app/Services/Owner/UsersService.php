<?php

namespace App\Services\Owner;
use ErrorException;
use App\Models\User;
use Auth;
use Session;

class UsersService {

    // Mengambil semua data users dengan role Pencari
    public function getAllUsers()
    {
        try {
            $users = User::where('role', 'Pencari')->get();
            // return $users;
            return view('pemilik.user.index', compact('users'));
        } catch (ErrorException $e) {
            throw new ErrorException($e->getMessage());
        }
    }

    // Mengambil data user berdasarkan ID
    public function getUserById($id)
    {
        try {
            $user = User::findOrFail($id);
            return $user;
        } catch (ErrorException $e) {
            throw new ErrorException($e->getMessage());
        }
    }

    // Mengambil data user yang sedang login
    public function getAuthUser()
    {
        try {
            $authUser = Auth::user();
            if (!$authUser) {
                throw new ErrorException('User tidak ditemukan');
            }
            return $authUser;
        } catch (ErrorException $e) {
            throw new ErrorException($e->getMessage());
        }
    }

    // Menampilkan halaman index users
    public function index()
    {
        try {
            $authUser = $this->getAuthUser();
            $users = $this->getAllUsers();

            if ($users->isEmpty()) {
                Session::flash('error', 'Data User Kosong');
                return redirect('/home');
            }

            return view('pemilik.user.index', [
                'users' => $users,
                'authUser' => $authUser
            ]);

        } catch (ErrorException $e) {
            Session::flash('error', $e->getMessage());
            return redirect('/home');
        }
    }
}

// edit