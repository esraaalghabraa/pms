<?php

namespace App\Models\Transaction;

use App\Models\Drug\Drug;
use App\Models\Registration\Repository;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepositoryStorage extends Model
{
    use HasFactory;

    protected $guarded=[];
    protected $hidden=['updated_at','created_at', 'repository_id','pivot'];


    public function drug()
    {
        return $this->belongsTo(Drug::class);
    }

    public function repository()
    {
        return $this->belongsTo(Repository::class);
    }

    public function batches()
    {
        return $this->hasMany(RepositoryBatch::class);
    }
}
