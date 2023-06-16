<?php

namespace App\Models\Registration;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class FrontUser extends Authenticatable
{
    use HasFactory , HasApiTokens;
    protected $guard = 'frontuser';
    const FRONT_USER_TOKEN = "frontUserToken";
    protected $guarded=[];
    protected $hidden = ['password','created_at','updated_at'];

    protected function Photo(): Attribute{
        return Attribute::make(
            get:fn ($value) => ($value != null) ? asset('assets/images/users/'. $value) : asset('assets/images/users/default_user.png')
        );
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
