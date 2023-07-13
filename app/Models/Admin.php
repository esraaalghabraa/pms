<?php

namespace App\Models;

use App\Notifications\RequestNotification;
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


    public function routeNotificationForOneSignal():array{
        return ['tags'=>['key'=>'adminUserId','relation'=>'=','value'=>(string)(1)]];
    }

    public function sendNewRequestNotification(array $data):void{
        $this->notify(new RequestNotification($data));
    }
}
