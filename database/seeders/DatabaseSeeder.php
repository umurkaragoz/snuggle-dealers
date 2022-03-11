<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(DefaultDataSeeder::class);
        
        User::factory()->admin()->count(3)->create();
        User::factory()->count(11)->create();
    }
}
