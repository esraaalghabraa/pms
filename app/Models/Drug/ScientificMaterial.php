<?php

namespace App\Models\Drug;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ScientificMaterial extends Model
{
    use HasFactory;
    protected $table='scientific_materials';
    protected $guarded=[];
    protected $hidden=['updated_at','created_at','pivot'];

    public function drugs():BelongsToMany{
        return $this->belongsToMany(Drug::class,ScientificMaterialDrug::class);
    }
}
