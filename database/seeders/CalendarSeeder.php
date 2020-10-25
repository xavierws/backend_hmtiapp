<?php

namespace Database\Seeders;

use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CalendarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $period = CarbonPeriod::create(now(), '2021-12-31');

        foreach ($period as $date) {
            DB::table('calendars')->insert([
                'date' => $date,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
