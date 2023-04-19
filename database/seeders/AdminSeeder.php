<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $exist = User::where('email','admin@gmail.com')->first();
        if (empty($exist)){
            $exist = new User();
            $exist->name = 'Admin';
            $exist->email = 'admin@gmail.com';
            $exist->password = Hash::make('12345678');
            $exist->role = 'Admin';
            $exist->save();
        }
    }
}
