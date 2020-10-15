<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role_list = $this->_get_roles_data();
        DB::table('roles')->insert($role_list);
    }

    /**
     * Provide the 'roles' table data
     *
     * @return array[]
     */
    protected function _get_roles_data(){
        return [
            ['id' => 1, 'name' => 'colleger', 'created_at' => now()],
            ['id' => 2, 'name' => 'admin', 'created_at' => now()]
        ];
    }
}
