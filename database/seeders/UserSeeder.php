<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();
        $user = [
            'name' => 'Donyor',
            'last_name'=> 'Abdulloev',
            'email' => 'donyora53@gmail.com',
            'balance' => 100
        ];
        User::create($user);
    }
}
