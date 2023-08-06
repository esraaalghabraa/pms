<?php

namespace App\Models;

use App\Notifications\RequestNotification;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;
    protected $guard = 'admin';
    const ADMIN_TOKEN = "adminToken";
    protected $guarded=[];
    protected $hidden=['created_at','updated_at'];



    protected function Photo(): Attribute{
        return Attribute::make(
            get:fn ($value) => ($value != null) ? asset('assets/images/admins/'. $value) : asset('assets/images/users/default_user.png')
        );
    }

    public function routeNotificationForOneSignal():array{
        return ['tags'=>['key'=>'adminUserId','relation'=>'=','value'=>(string)(1)]];
    }

//    public function sendNewRequestNotification(array $data):void{
//        $this->notify(new RequestNotification($data));
//    }
}
