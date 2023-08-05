<?php

namespace App\Models\Registration;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guarded=[];
    protected $hidden=['password','email_verified_at','rememberToken','created_at','updated_at','pivot'];
    const USER_TOKEN = "userToken";


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
