<?php

namespace App\Models\Transaction;

use App\Models\RepositoryStorage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestItem extends Model
{
    use HasFactory;
    public function drugReguest()
    {
        return $this->belongsTo(DrugRequest::class,'drug_request_id');
    }

    public function repositoryStorage()
    {
        return $this->belongsTo(RepositoryStorage::class);
    }
}
