<?php

namespace App\Models\Registration;
;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends \Spatie\Permission\Models\Role
{
    use HasFactory;
    protected $guard_name = 'user';
    protected $guarded = [];
    protected $hidden=['created_at','updated_at'];


    const ROLE_ADMIN = 1;
    const ROLE_Supplier = 2;
    const ROLE_Employee = 3;




}
