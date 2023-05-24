<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i=1;$i<=10;$i++) {
            User::create([
                'name' => fake()->firstName(),
                'email' => fake()->unique()->safeEmail(),
                'password'=>Hash::make('123456789'),
                'verify_code'=>rand(1000,2000),
                'email_verified_at' => Carbon::now(),
                'role_id' => rand(1,3), // Administrator
            ]);
        }
    }
}
