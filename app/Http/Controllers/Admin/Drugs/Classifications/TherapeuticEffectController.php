<?php

namespace App\Http\Controllers\Admin\Drugs\Classifications;

use App\Http\Controllers\Controller;
use App\Models\Drug\TherapeuticEffect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TherapeuticEffectController extends Controller
{

    public function get(){
        $therapeutic_effects = TherapeuticEffect::get();
        return $this->success($therapeutic_effects);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:therapeutic_effects,name',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        TherapeuticEffect::create([
            'name' => $request->name
        ]);

        return $this->success();
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:therapeutic_effects',
            'name' => 'required|string|max:50|unique:therapeutic_effects,name,' . $request->id,
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $therapeutic_effect = TherapeuticEffect::where('id',$request->id)->first();
        $therapeutic_effect->update([
            'name' => $request->name
        ]);

        return $this->success();
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:therapeutic_effects',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        TherapeuticEffect::where('id',$request->id)->first()->delete();
        return $this->success();
    }

    public function getDrugs(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:therapeutic_effects',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $drugs = TherapeuticEffect::with(['drugs'=>function($q)
        {return $q->select('drugs.id','brand_name');}])
            ->where('id', $request->id)
            ->select('id')
            ->first();

        return $this->success($drugs);
    }

}
