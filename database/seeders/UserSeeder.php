<?php

namespace Database\Seeders;

use App\Models\AdministratorProfile;
use App\Models\CollegerProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CollegerProfile::factory()->count(300)->create()->each(function ($profile) {
            User::factory()->state([
                'userable_id' => $profile->id
            ])->create();
        });

        AdministratorProfile::factory()->count(1)->create();
        DB::table('users')->insert([
            'id' => 301,
            'email' => 'mail@example.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'userable_id' => 1,
            'userable_type' => 'App\Models\AdministratorProfile'
        ]);
    }
}
