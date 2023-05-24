<?php

namespace Database\Seeders;

use App\Models\Drug\Indication;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IndicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Indication::truncate();
        Indication::create([
            'name'=>'headache',
        ]);
        Indication::create([
            'name'=>'hyperthermia',
        ]);
        Indication::create([
            'name'=>'joint pain',
        ]);
    }
}
