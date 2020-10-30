<?php

namespace Database\Seeders;

use App\Models\AdministratorProfile;
use App\Models\CollegerProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
//        CollegerProfile::factory()->count(300)->create()->each(function ($profile) {
//            User::factory()->state([
//                'userable_id' => $profile->id
//            ])->create();
//        });
//
//        AdministratorProfile::factory()->count(1)->create();
//        DB::table('users')->insert([
//            'id' => 301,
//            'email' => env('MAIL_USER_ADMIN'),
//            'email_verified_at' => now(),
//            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
//            'remember_token' => Str::random(10),
//            'userable_id' => 1,
//            'userable_type' => 'App\Models\AdministratorProfile'
//        ]);

        $this->generateColleger();
        $this->generateAdmin();
    }

    private function generateColleger() {
        $path = 'database/seeders/dataset/data-hmti.csv';
        $array = $fields = array();
        $i = 0;
        $handle = @fopen($path, "r");
        if ($handle) {
            while (($row = fgetcsv($handle, 4096)) !== false) {
                if (empty($fields)) {
                    $fields = $row;
                    continue;
                }
                foreach ($row as $k=>$value) {
                    $array[$i][$fields[$k]] = $value;
                }
                $i++;
            }
            if (!feof($handle)) {
                echo "Error: unexpected fgets() fail\n";
            }
            fclose($handle);
        }

        foreach ($array as $row) {
            $nrp = (int)$row['nrp'];
            $birthday = date('Y-m-d', strtotime($row['birthday']));

            DB::table('colleger_profiles')->insert([
                'name' => $row['name'],
                'nrp'=> $nrp,
                'birthday' => $birthday=='1970-01-01'? null:$birthday,
                'address' => $row['address']===''? null:$row['address'],
                'role_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $colleger = CollegerProfile::orderBy('id', 'desc')->first();

            DB::table('users')->insert([
                'email' => $row['email'],
                'email_verified_at' => now(),
                'password' => Hash::make($nrp),
                'remember_token' => Str::random(10),
                'userable_id' => $colleger->id,
                'userable_type' => 'App\Models\CollegerProfile',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function generateAdmin() {
        DB::table('administrator_profiles')->insert([
            [
                'name' => 'HMTI-ITS 1',
                'role_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'HMTI-ITS 2',
                'role_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'HMTI-ITS 3',
                'role_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        DB::table('users')->insert([
            [
                'email' => 'hosthmtiits@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('hmti_admin'),
                'remember_token' => Str::random(10),
                'userable_id' => 1,
                'userable_type' => 'App\Models\AdministratorProfile',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'email' => 'hmtiits1920@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('hmti_admin'),
                'remember_token' => Str::random(10),
                'userable_id' => 2,
                'userable_type' => 'App\Models\AdministratorProfile',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
