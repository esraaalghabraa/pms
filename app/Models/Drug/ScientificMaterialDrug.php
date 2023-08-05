<?php

namespace App\Models\Drug;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScientificMaterialDrug extends Model
{
    use HasFactory;
    protected $table='scientific_materials_drugs';
    protected $guarded=[];
    protected $hidden=['created_at','updated_at'];

}
