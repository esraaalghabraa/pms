<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyBatch extends Model
{
    // TODO duplicated
    protected $guarded=[];
    protected $hidden = ['created_at', 'updated_at', 'pivot'];

    use HasFactory;
}
