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


    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:dosage_forms,name',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        DosageForm::create([
            'name' => $request->name
        ]);

        return $this->success();
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:dosage_forms',
            'name' => 'required|string|max:50|unique:dosage_forms,name,' . $request->id,
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $dosage_form = DosageForm::where('id', $request->id)->first();
        $dosage_form->update([
            'name' => $request->name
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
            return $q->select('drugs.id', 'brand_name');
        }])
            ->where('id', $request->id)
            ->select('id')
            ->first();

        return $this->success($drugs);
    }

}
