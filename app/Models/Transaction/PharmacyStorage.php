<?php

namespace App\Models\Transaction;

use App\Models\Drug\Drug;
use App\Models\Registration\Pharmacy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyStorage extends Model
{
    use HasFactory;
    protected $guarded=[];

    public function drug()
    {
        return $this->belongsTo(Drug::class);
    }
    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }
}
