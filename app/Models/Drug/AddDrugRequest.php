<?php

namespace App\Models\Drug;

use App\Models\Registration\Repository;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AddDrugRequest extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded=[];
    protected $hidden=['created_at','updated_at'];


    function repository() : BelongsTo{
        return $this->belongsTo(Repository::class,'repository_id');
    }
}
