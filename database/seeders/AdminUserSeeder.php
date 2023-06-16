<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Registration\User;
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
      $data['name']='esraa';
      $data['email']='osamaaabdalmalik@gmail.com';
      $data['password']=bcrypt(123456789);
      Admin::create($data);
    }
}
