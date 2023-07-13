<?php

namespace App\Models\Drug;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TherapeuticEffect extends Model
{
    use HasFactory;
    protected $guarded=[];
    protected $hidden=['updated_at','created_at','pivot'];


    public function drugs():BelongsToMany{
        return $this->belongsToMany(Drug::class,'therapeutic_effects_drugs');
    }
}
