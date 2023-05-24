<?php

namespace Database\Seeders;

use App\Models\Drug\DosageForm;
use Illuminate\Database\Seeder;

class DosageFormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DosageForm::truncate();
        DosageForm::create([
            'name' => 'Tablets',
            'unit' => 'Tablet',
            'type' => 'Tablets and Capsules',
        ]);
        DosageForm::create([
            'name' => 'Capsules',
            'unit' => 'Capsule',
            'type' => 'Tablets and Capsules',
        ]);
        DosageForm::create([
            'name' => 'Syrups',
            'unit' => 'ml',
            'type' => 'Liquids',
        ]);
        DosageForm::create([
            'name' => 'Solutions',
            'unit' => 'ml',
            'type' => 'Liquids',
        ]);
        DosageForm::create([
            'name' => 'Suspensions',
            'unit' => 'ml',
            'type' => 'Liquids',
        ]);
        DosageForm::create([
            'name' => 'Elixirs',
            'unit' => 'ml',
            'type' => 'Liquids',
        ]);
        DosageForm::create([
            'name' => 'Big Ointments',
            'unit' => 'ml',
            'type' => 'Creams and Ointments',
        ]);
        DosageForm::create([
            'name' => 'Small Ointments',
            'unit' => 'ml',
            'type' => 'Creams and Ointments',
        ]);

        DosageForm::create([
            'name' => 'Big Creams',
            'unit' => 'g',
            'type' => 'Creams and Ointments',
        ]);
        DosageForm::create([
            'name' => 'Small Creams',
            'unit' => 'mg',
            'type' => 'Creams and Ointments',
        ]);
        DosageForm::create([
            'name' => 'Single Unit',
            'unit' => 'piece',
            'type' => 'Others',
        ]);
        DosageForm::create([
            'name' => 'Aerosols',
            'unit' => 'puff',
            'type' => 'Inhalation',
        ]);
        DosageForm::create([
            'name' => 'Intravenous',
            'unit' => 'ml',
            'type' => 'Injectables',
        ]);
        DosageForm::create([
            'name' => 'Intramuscular',
            'unit' => 'ml',
            'type' => 'Injectables',
        ]);
    }
}
