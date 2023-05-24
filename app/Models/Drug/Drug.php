<?php

namespace App\Models\Drug;

use App\Models\Registration\Pharmacy;
use App\Models\Registration\Repository;
use App\Models\Transaction\PharmacyStorage;
use App\Models\Transaction\RepositoryStorage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Drug extends Model
{
    use HasFactory;
    protected $guarded=[];

    public function category():BelongsTo
    {
        return $this->belongsTo(Category::class,'category_id');
    }

    public function dosageForm():BelongsTo
    {
        return $this->belongsTo(DosageForm::class,'dosage_form_id');
    }

    public function manufactureCompany():BelongsTo
    {
        return $this->belongsTo(ManufactureCompany::class,'manufacture_company_id');
    }

    public function indications():BelongsToMany{
        return $this->belongsToMany(Indication::class,'indications_drugs');
    }

    public function scientificMaterials():BelongsToMany{
        return $this->belongsToMany(Drug::class,'scientific_materials_drugs');
    }

    public function therapeuticEffects():BelongsToMany{
        return $this->belongsToMany(Drug::class,'therapeutic_effects_drugs');
    }

    public function pharmacies():BelongsToMany
    {
        return $this->belongsToMany(Pharmacy::class,PharmacyStorage::class);
    }
    public function repositories():BelongsToMany
    {
        return $this->belongsToMany(Repository::class,RepositoryStorage::class);
    }

}
