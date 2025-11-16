<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'full_name'=> 'test',
            'username' => 'test',
            'password' => Hash::make('test'),
            'access_token' => 'test'
        ]);
        User::create([
            'full_name'=> 'test2',
            'username' => 'test2',
            'password' => Hash::make('test2'),
            'access_token' => 'test2'
        ]);
    }
}
