<?php

namespace Database\Seeders;

use App\Models\Drug\ScientificMaterial;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScientificMaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ScientificMaterial::truncate();
        ScientificMaterial::create([
            'name'=>'Insulin',
        ]);
        ScientificMaterial::create([
            'name'=>'aspirin',
        ]);
        ScientificMaterial::create([
            'name'=>'cetacodeine',
        ]);
    }
}
