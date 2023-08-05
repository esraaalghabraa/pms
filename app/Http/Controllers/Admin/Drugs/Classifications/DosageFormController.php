<?php

namespace App\Http\Controllers\Admin\Drugs\Classifications;

use App\Http\Controllers\Controller;
use App\Models\Drug\DosageForm;
use App\Models\Drug\Drug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DosageFormController extends Controller
{

    public function get()
    {
        $dosage_forms = DosageForm::get();
        return $this->success($dosage_forms);
    }

    public function getUnitsAndTypes()
    {
        $units = DosageForm::select('unit')->distinct()->get();
        $types = DosageForm::select('type')->distinct()->get();
        return $this->success([
            'units'=>$units,
            'types'=>$types,
        ]);
    }


    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:dosage_forms,name',
            'unit' => 'required|string|max:50|exists:dosage_forms,unit',
            'type' => 'required|string|max:50|exists:dosage_forms,type',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        DosageForm::create([
            'name' => $request->name,
            'unit' => $request->unit,
            'type' => $request->type,
        ]);

        return $this->success();
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:dosage_forms',
            'name' => 'required|string|max:50|unique:dosage_forms,name,' . $request->id,
            'unit' => 'required|string|max:50|exists:dosage_forms,unit',
            'type' => 'required|string|max:50|exists:dosage_forms,type',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $dosage_form = DosageForm::where('id', $request->id)->first();
        $dosage_form->update([
            'name' => $request->name,
            'unit' => $request->unit,
            'type' => $request->type,
        ]);

        return $this->success();
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:dosage_forms',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        DosageForm::where('id', $request->id)->first()->delete();
        return $this->success();
    }

    public function getDrugs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:dosage_forms',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $drugs = DosageForm::with(['drugs' => function ($q) {
            return $q->with('category')
                ->with(['dosageForm'=>function($q){
                    return $q->select('id','name');
                }])
                ->with('manufactureCompany')
                ->with('indications')
                ->with('scientificMaterials')
                ->with('therapeuticEffects')
                ->get();
        }])
            ->where('id', $request->id)
            ->select('id')
            ->first()->drugs;

        return $this->success($drugs);
    }

}
