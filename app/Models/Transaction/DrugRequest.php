<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrugRequest extends Model
{
    use HasFactory;
    protected $guarded=[];

    public function requestItems()
    {
        return $this->hasMany(DrugRequest::class);
    }

    public function buyBill()
    {
        return $this->belongsTo(BuyBill::class,'buy_bill_id');
    }

}
