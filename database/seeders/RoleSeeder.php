<?php

namespace Database\Seeders;

use App\Models\Registration\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create(['name' => 'pharmacy']);
        Role::create(['name' => 'repository']);
    }
}
