<?php

namespace App\Models\Transaction;

use App\Models\Drug\Drug;
use App\Models\Registration\Pharmacy;
use App\Models\Registration\Repository;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepositoryStorage extends Model
{
    use HasFactory;

    public function drug()
    {
        return $this->belongsTo(Drug::class);
    }

    public function repository()
    {
        return $this->belongsTo(Repository::class);
    }
}
