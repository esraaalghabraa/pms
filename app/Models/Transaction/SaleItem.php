<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;
    protected $guarded=[];
    protected $hidden=['created_at','updated_at'];


    public function saleBill()
    {
        return $this->belongsTo(SaleBill::class);
    }
    public function pharmacyStorage()
    {
        return $this->belongsTo(PharmacyStorage::class);
    }
}
