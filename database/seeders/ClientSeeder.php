<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\User::all()->each(function ($user) {
            Client::factory()->count(5)->create(['user_id' => $user->id]);
        });
    }
}
