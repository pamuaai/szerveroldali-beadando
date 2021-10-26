<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // TODO: Admin user
        // User::factory(10)->create();
        for ($i = 1; $i <= 10; $i++) {
            User::factory()->create([
                'name' => 'user' . $i,
                'email' => 'user' . $i . '@szerveroldali.hu',
                'is_admin' => false,
            ]);
        }
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@szerveroldali.hu',
            'is_admin' => true,
        ]);
    }
}
