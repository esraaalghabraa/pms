<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyBatch extends Model
{
    use HasFactory;
    protected $guarded=[];


    public function pharmacyStorage()
    {
        return $this->belongsTo(PharmacyStorage::class,'pharmacy_storage_id');
    }
}
