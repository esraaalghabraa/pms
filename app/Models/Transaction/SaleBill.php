<?php

namespace App\Models\Transaction;

use App\Models\Registration\Pharmacy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SaleBill extends Model
{
    use HasFactory;
    protected $guarded=[];
    protected $hidden=['created_at','updated_at'];

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function customer():BelongsTo
    {
        return $this->belongsTo(Customer::class,'customer_id');
    }
}
