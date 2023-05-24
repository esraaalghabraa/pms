<?php

namespace App\Models\Registration;

use App\Models\Drug\Drug;
use App\Models\Transaction\PharmacyStorage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pharmacy extends Model
{
    use HasFactory;
    protected $guarded=[];

    public function owner():BelongsTo
    {
        return $this->belongsTo(Role::class,'owner_id');
    }

    public function drugs():BelongsToMany
    {
        return $this->belongsToMany(Drug::class,PharmacyStorage::class);
    }

}
