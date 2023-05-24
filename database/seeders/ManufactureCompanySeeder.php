<?php

namespace Database\Seeders;

use App\Models\Drug\ManufactureCompany;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ManufactureCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ManufactureCompany::truncate();
        ManufactureCompany::create([
            'name' => 'Alfa Laboratories',
        ]);
        ManufactureCompany::create([
            'name' => 'Ibn Al-Haytham',
        ]);
        ManufactureCompany::create([
            'name' => 'Sham Pharma',
        ]);
        ManufactureCompany::create([
            'name' => 'Ibn-Alhaytham',
        ]);
    }
}
