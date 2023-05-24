<?php

namespace App\Models\Drug;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ManufactureCompany extends Model
{
    use HasFactory;

    public function drugs():HasMany{
        return $this->hasMany(Drug::class);
    }
}
