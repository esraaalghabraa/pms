<?php

namespace App\Models\Transaction;

use App\Models\Registration\Pharmacy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;
    protected $guarded=[];
    protected $hidden=['created_at','updated_at'];

    function saleBills() : HasMany{
        return  $this->hasMany(SaleBill::class,);
    }

    function pharmacies() : BelongsToMany{
        return  $this->belongsToMany(Pharmacy::class,SaleBill::class);
    }

}
