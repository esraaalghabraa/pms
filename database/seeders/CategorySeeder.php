<?php

namespace Database\Seeders;

use App\Models\Drug\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::truncate();
        Category::create([
            'name'=>'Drugs',
        ]);
        Category::create([
            'name'=>'Cosmetics',
        ]);
        Category::create([
            'name'=>'Medicinal products',
        ]);
    }
}
