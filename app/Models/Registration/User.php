<?php

namespace App\Models\Registration;

use App\Notifications\RequestNotification;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,HasRoles;
    protected $guard = 'user';
    protected $guard_name = 'user';
    protected $guarded=[];
    protected $hidden=['password','email_verified_at','remember_token','verify_code','rememberToken','created_at','updated_at','pivot'];
    const USER_TOKEN = "userToken";


    protected function Photo(): Attribute{
        return Attribute::make(
            get:fn ($value) => ($value != null) ? asset('assets/images/users/'. $value) : asset('assets/images/users/default_user.png')
        );
    }
    public function pharmacies():BelongsToMany
    {
        return $this->belongsToMany(Pharmacy::class,'pharmacies_users','user_id','pharmacy_id',);
    }

    public function sendNewRequestNotification(array $data):void{
        $this->notify(new RequestNotification($data));
    }

}
