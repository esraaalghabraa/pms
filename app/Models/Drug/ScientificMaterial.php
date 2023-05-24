<?php

namespace App\Models\Drug;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ScientificMaterial extends Model
{
    use HasFactory;

    public function drugs():BelongsToMany{
        return $this->belongsToMany(Drug::class,'scientific_materials_drugs');
    }
}
