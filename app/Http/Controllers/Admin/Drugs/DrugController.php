<?php

namespace App\Http\Controllers\Admin\Drugs;

use App\Http\Controllers\Controller;
use App\Models\Drug\Drug;
use App\Models\Drug\IndicationDrug;
use App\Models\Drug\ScientificMaterialDrug;
use App\Models\Drug\TherapeuticEffectDrug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DrugController extends Controller
{

    public function get(){
        $drugs = Drug::with('indications')
            ->with('scientificMaterials')
            ->with('therapeuticEffects')
            ->get();
        return $this->success($drugs);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'brand_name' => 'required|string|max:50',
            'scientific_name' => 'required|string|max:50',
            'capacity' => 'required|string|max:50',
            'titer' => 'required|string|max:50',
            'contraindications' => 'required|string',
            'is_prescription' => 'required',
            'category_id' => 'required|exists:categories,id',
            'dosage_form_id' => 'required|exists:dosage_forms,id',
            'manufacture_company_id' => 'required|exists:manufacture_companies,id',
            'scientific_materials'=> 'required',
            'therapeutic_effects'=> 'required',
            'indications'=> 'required',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
       $drug =  Drug::create([
            'brand_name' => $request->brand_name,
            'scientific_name' => $request->scientific_name,
           'capacity' => $request->capacity,
           'titer' => $request->scientific_name,
           'is_prescription' => $request->is_prescription,
           'contraindications' => $request->contraindications,
            'category_id' => $request->category_id,
            'dosage_form_id' => $request->dosage_form_id,
            'manufacture_company_id' => $request->manufacture_company_id
        ]);
       $scientificMaterials = json_decode($request->scientific_materials);
       $therapeuticEffects = json_decode($request->therapeutic_effects);
       $indications = json_decode($request->indications);
       foreach ($scientificMaterials as $scientificMaterial)
           ScientificMaterialDrug::create([
               'drug_id'=>$drug->id,
               'scientific_material_id'=>$scientificMaterial->id,
           ]);
       foreach ($therapeuticEffects as $therapeuticEffect)
           TherapeuticEffectDrug::create([
               'drug_id'=>$drug->id,
               'therapeutic_effect_id'=>$therapeuticEffect->id,
           ]);
       foreach ($indications as $indication)
           IndicationDrug::create([
               'drug_id'=>$drug->id,
               'indication_id'=>$indication->id,
           ]);
        return $this->success();
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:drugs',
            'brand_name' => 'required|string|max:50',
            'capacity' => 'required|string|max:50',
            'titer' => 'required|string|max:50',
            'is_prescription' => 'required',
            'contraindications' => 'required|string',
            'scientific_name' => 'required|string|max:50',
            'category_id' => 'required|exists:categories,id',
            'dosage_form_id' => 'required|exists:dosage_forms,id',
            'manufacture_company_id' => 'required|exists:manufacture_companies,id',
            'scientific_materials'=> 'required',
            'therapeutic_effects'=> 'required',
            'indications'=> 'required',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $drug = Drug::where('id',$request->id)
            ->with('indications')
            ->with('scientificMaterials')
            ->with('therapeuticEffects')
            ->first();
        $drug->update([
            'brand_name' => $request->brand_name,
            'scientific_name' => $request->scientific_name,
            'capacity' => $request->capacity,
            'titer' => $request->scientific_name,
            'is_prescription' => $request->is_prescription,
            'contraindications' => $request->contraindications,
            'category_id' => $request->category_id,
            'dosage_form_id' => $request->dosage_form_id,
            'manufacture_company_id' => $request->manufacture_company_id
        ]);
        $drug->save();

        foreach ($drug->scientificMaterials as $scientific_material) {
            ScientificMaterialDrug::where('scientific_material_id', $scientific_material->id)->delete();
        }
        foreach ($drug->therapeuticEffects as $therapeutic_effect) {
            TherapeuticEffectDrug::where('therapeutic_effect_id', $therapeutic_effect->id)->delete();
        }
        foreach ($drug->indications as $indication) {
            IndicationDrug::where('indication_id', $indication->id)->delete();
        }
       $scientificMaterials = json_decode($request->scientific_materials);
       $therapeuticEffects = json_decode($request->therapeutic_effects);
       $indications = json_decode($request->indications);

       foreach ($scientificMaterials as $scientificMaterial)
           ScientificMaterialDrug::create([
               'drug_id'=>$drug->id,
               'scientific_material_id'=>$scientificMaterial->id,
           ]);
       foreach ($therapeuticEffects as $therapeuticEffect)
           TherapeuticEffectDrug::create([
               'drug_id'=>$drug->id,
               'therapeutic_effect_id'=>$therapeuticEffect->id,
           ]);
       foreach ($indications as $indication)
           IndicationDrug::create([
               'drug_id'=>$drug->id,
               'indication_id'=>$indication->id,
           ]);
        return $this->success();
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:drugs',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $drug = Drug::where('id',$request->id)
            ->with('indications')
            ->with('scientificMaterials')
            ->with('therapeuticEffects')
            ->first();
        if ($drug->scientificMaterials)
        foreach ($drug->scientificMaterials as $scientific_material) {
            ScientificMaterialDrug::where('scientific_material_id', $scientific_material->id)->delete();
        }
        if ($drug->therapeuticEffects)
        foreach ($drug->therapeuticEffects as $therapeutic_effect) {
            TherapeuticEffectDrug::where('therapeutic_effect_id', $therapeutic_effect->id)->delete();
        }
        if ($drug->indications)
        foreach ($drug->indications as $indication) {
            IndicationDrug::where('indication_id', $indication->id)->delete();
        }
        $drug->delete();
        return $this->success();
    }

}
