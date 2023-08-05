<?php

namespace App\Http\Controllers\Admin\Drugs\Classifications;

use App\Http\Controllers\Controller;
use App\Models\Drug\Drug;
use App\Models\Drug\ManufactureCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ManufactureCompanyController extends Controller
{

    public function get(){
        $manufacture_companies = ManufactureCompany::get();
        return $this->success($manufacture_companies);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:manufacture_companies,name',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        ManufactureCompany::create([
            'name' => $request->name
        ]);
        return $this->success();
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:manufacture_companies',
            'name' => 'required|string|max:50|unique:manufacture_companies,name,' . $request->id,
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $manufacture_company = ManufactureCompany::where('id',$request->id)->first();
        $manufacture_company->update([
            'name' => $request->name
        ]);
        return $this->success();
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:manufacture_companies',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        ManufactureCompany::where('id',$request->id)->first()->delete();
        return $this->success();
    }

    public function getDrugs(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:manufacture_companies',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $drugs = ManufactureCompany::with(['drugs' => function ($q) {
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
