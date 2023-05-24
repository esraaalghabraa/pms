<?php

namespace App\Models\Drug;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Indication extends Model
{
    use HasFactory;

    public function drugs():BelongsToMany{
        return $this->belongsToMany(Drug::class,'indications_drugs');
    }
}
