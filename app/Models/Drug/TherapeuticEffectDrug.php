<?php

namespace App\Models\Drug;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TherapeuticEffectDrug extends Model
{
    use HasFactory;
    protected $table='therapeutic_effects_drugs';
    protected $guarded=[];
    protected $hidden=['created_at','updated_at'];

}
