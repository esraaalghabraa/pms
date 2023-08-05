<?php

namespace App\Models\Transaction;

use App\Models\ItemBatch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepositoryBatch extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at', 'pivot'];

    public function repositoryStorage()
    {
        return $this->belongsTo(RepositoryStorage::class, 'repository_storage_id');
    }

    public function batches()
    {
        return $this->hasMany(PharmacyStorage::class);
    }

    public function requestItems()
    {
        return $this->belongsToMany(RequestItem::class, ItemBatch::class, 'item_id', 'batch_id');
    }

    public function quantityItems()
    {
        return $this->hasMany( ItemBatch::class, 'batch_id');
    }


}
