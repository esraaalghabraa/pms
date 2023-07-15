<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestItem extends Model
{
    use HasFactory;
    protected $guarded=[];
    protected $hidden = ['created_at', 'updated_at', 'drug_request_id'];

    public function drugReguest()
    {
        return $this->belongsTo(DrugRequest::class,'drug_request_id');
    }

    public function repositoryStorage()
    {
        return $this->belongsTo(RepositoryStorage::class);
    }
}
