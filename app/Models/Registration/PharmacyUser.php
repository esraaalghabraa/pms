<?php

namespace App\Models\Registration;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyUser extends Model
{
    use HasFactory;
    protected $guarded=[];
    protected $table='pharmacies_users';
}
