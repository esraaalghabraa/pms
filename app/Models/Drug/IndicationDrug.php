<?php

namespace App\Models\Drug;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndicationDrug extends Model
{
    use HasFactory;
    protected $table='indications_drugs';
    protected $guarded=[];

}
