<?php

namespace Database\Seeders;

use App\Models\Drug\TherapeuticEffect;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TherapeuticEffectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TherapeuticEffect::create([
            'name'=>'Painkillers',
        ]);
        TherapeuticEffect::create([
            'name'=>'antipyretic',
        ]);
    }
}
