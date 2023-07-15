<?php

namespace App\Models\Registration;

use App\Models\Drug\Drug;
use App\Models\Transaction\RepositoryStorage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Repository extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $hidden=['updated_at','created_at','owner_id'];

    public function owner()
    {
        return $this->belongsTo(Role::class,'owner_id');
    }

    public function medicineStorages(): HasMany
    {
        return $this->hasMany(RepositoryStorage::class);
    }

    public function drugs():BelongsToMany
    {
        return $this->belongsToMany(Drug::class,RepositoryStorage::class);
    }
}
