<?php

namespace App\Models\Registration;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $hidden=['created_at','updated_at'];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
