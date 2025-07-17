<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Invoice;
use Database\Seeders\InvoiceSeeder; 

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Nishant Patel',
            'email' => 'nishant.patel@pderas.com',
            'password' => bcrypt('password'), // password
        ]);

        $this->call(InvoiceSeeder::class);
        $this->call(ClientSeeder::class);
    }

}
