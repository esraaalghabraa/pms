<?php

namespace App\Models\Registration;

use App\Models\Drug\Drug;
use App\Models\PharmacyCustomer;
use App\Models\Transaction\Customer;
use App\Models\Transaction\PharmacyStorage;
use App\Models\Transaction\SaleBill;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pharmacy extends Model
{
    use HasFactory;
    protected $guarded=[];

    public function users():BelongsToMany
    {
        return $this->belongsToMany(User::class,'pharmacies_users','pharmacy_id','user_id');
    }

    public function drugs():BelongsToMany
    {
        return $this->belongsToMany(Drug::class,PharmacyStorage::class);
    }

    function customers() : BelongsToMany{
        return  $this->belongsToMany(Customer::class,PharmacyCustomer::class);
    }
}
