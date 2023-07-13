<?php

namespace Database\Seeders;

use App\Models\Registration\Repository;
use App\Models\Registration\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // supplier
        $user = User::create([
            'name' => 'Osama',
            'email' => "osama@gmail.com",
            'password'=>Hash::make('123456789'),
            'verify_code'=>rand(1000,2000),
            'email_verified_at' => Carbon::now(),
            'role_id' => 2, // Administrator
        ]);

        Repository::create([
            'name'=>'Repo1',
            'address'=>'RepoAddress',
            'phone'=>'0996597853',
            'owner_id'=> $user->id,
        ]);
    }
}
