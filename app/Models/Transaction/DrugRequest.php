<?php

namespace App\Models\Transaction;

use App\Models\Registration\Pharmacy;
use App\Models\Registration\Repository;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DrugRequest extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at', 'pharmacy_id', 'repository_id', 'buy_bill_id'];

    public function requestItems(): HasMany
    {
        return $this->hasMany(RequestItem::class);
    }

    public function pharmacy(): BelongsTo
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function repository(): BelongsTo
    {
        return $this->belongsTo(Repository::class);
    }

    public function buyBill()
    {
        return $this->belongsTo(BuyBill::class, 'buy_bill_id');
    }

}
