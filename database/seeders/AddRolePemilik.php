<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class AddRoleAdmin extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user =  User::create([
            'name'  => 'Pemilik',
            'email' => 'pemilik@gmail.com',
            'role'  => 'Pemilik',
            'password' => Hash::make('password'),
        ]);

        $role = Role::create([
            'name'  => 'Pemilik'
        ]);

        $user->assignRole($role);
    }
}
