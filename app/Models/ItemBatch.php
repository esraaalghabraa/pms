<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemBatch extends Model
{
    use HasFactory;
    protected $table='item_batch';
    protected $guarded=[];
    protected $hidden=['created_at','updated_at','pivot'];



}
