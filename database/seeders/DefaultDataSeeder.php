<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DefaultDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /* ------------------------------------------------------------------------------------------------------------------------------- user -+- */
        User::create([
                'email'             => 'admin@buckhill.co.uk',
                'first_name'        => 'Buckhill',
                'last_name'         => 'Admin',
                'is_admin'          => 1,
                'email_verified_at' => now(),
                'address'           => '3rd Floor, 86-90 Paul Street, London, EC2A 4NE',
                'phone_number'      => '+44(0)1903 250 250',
                'password'          => bcrypt('admin'),
            ]
        );
    }
}
