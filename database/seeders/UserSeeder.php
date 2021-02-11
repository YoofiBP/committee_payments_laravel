<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    const DEFAULT_ID = 1;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        User::create([
            'id' => self::DEFAULT_ID,
            'name' => 'Test User',
            'email' => 'test@email.com',
            'password' => 'password',
            'phone_number' => '+233248506381'
        ]);
    }
}
