<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    use HasFactory;
    protected $guarded=[];

    public function saleBill():BelongsTo
    {
        return $this->belongsTo(SaleBill::class);
    }
    public function pharmacyBatch():BelongsTo
    {
        return $this->belongsTo(PharmacyBatch::class);
    }
}
