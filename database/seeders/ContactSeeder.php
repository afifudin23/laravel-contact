<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('username', 'test')->first();
        Contact::create([
            'first_name' => 'first test',
            'last_name' => 'last test',
            'email' => 'emailtest@mail.co',
            'phone' => '123456789101',
            'user_id' => $user->id,
        ]);
    }
}
