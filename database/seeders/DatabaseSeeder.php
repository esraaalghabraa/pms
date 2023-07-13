<?php

namespace Database\Seeders;


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
        $this->call(RoleSeeder::class);
        $this->call(AdminUserSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(DosageFormSeeder::class);
        $this->call(IndicationSeeder::class);
        $this->call(ManufactureCompanySeeder::class);
        $this->call(ScientificMaterialSeeder::class);
        $this->call(TherapeuticEffectSeeder::class);


    }
}
