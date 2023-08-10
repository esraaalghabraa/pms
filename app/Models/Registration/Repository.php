<?php

namespace App\Models\Registration;

use App\Models\Drug\Drug;
use App\Models\Transaction\DrugRequest;
use App\Models\Transaction\RepositoryStorage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Repository extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $hidden=['updated_at','created_at','user_id'];

    public function owner():BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function RepositoryStorages(): HasMany
    {
        return $this->hasMany(RepositoryStorage::class);
    }

    public function drugs():BelongsToMany
    {
        return $this->belongsToMany(Drug::class,RepositoryStorage::class);
    }

    public function drugRequests():HasMany
    {
        return $this->hasMany(DrugRequest::class);
    }
}
