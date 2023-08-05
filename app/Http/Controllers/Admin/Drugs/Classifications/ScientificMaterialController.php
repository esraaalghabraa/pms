<?php

namespace App\Http\Controllers\Admin\Drugs\Classifications;

use App\Http\Controllers\Controller;
use App\Models\Drug\ScientificMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScientificMaterialController extends Controller
{
    public function get(){
        $categories = ScientificMaterial::get();
        return $this->success($categories);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:scientific_materials,name,' . $request->id,
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        ScientificMaterial::create([
            'name' => $request->name
        ]);
        return $this->success();
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:scientific_materials',
                'name' => 'required|string|max:50|unique:scientific_materials,name,' . $request->id,
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $category = ScientificMaterial::where('id',$request->id)->first();
        $category->update([
            'name' => $request->name
        ]);

        return $this->success();
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:scientific_materials',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        ScientificMaterial::where('id',$request->id)->first()->delete();
        return $this->success();
    }

    public function getDrugs(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:scientific_materials',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
         $drugs = ScientificMaterial::with(['drugs' => function ($q) {
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
