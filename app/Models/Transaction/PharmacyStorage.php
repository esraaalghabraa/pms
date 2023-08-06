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
    protected $hidden=['updated_at','created_at', 'pharmacy_id','pivot'];

    public function drug()
    {
        return $this->belongsTo(Drug::class);
    }
    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }
    public function batches()
    {
        return $this->hasMany(PharmacyBatch::class);
    }
}
