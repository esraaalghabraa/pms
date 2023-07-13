<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepositoryBatch extends Model
{
    use HasFactory;
    protected $guarded=[];

    public function repositoryStorage()
    {
        return $this->belongsTo(RepositoryStorage::class,'repository_storage_id');
    }

    public function batches()
    {
        return $this->hasMany(PharmacyStorage::class);
    }

}
