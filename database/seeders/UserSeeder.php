<?php

namespace Database\Seeders;

use App\Models\CollegerProfile;
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
        CollegerProfile::factory()->count(300)->create()->each(function ($profile) {
            User::factory()->state([
                'userable_id' => $profile->id
            ])->create();
        });
    }
}
