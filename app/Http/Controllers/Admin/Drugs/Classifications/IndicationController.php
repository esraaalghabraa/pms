<?php

namespace App\Http\Controllers\Admin\Drugs\Classifications;

use App\Http\Controllers\Controller;
use App\Models\Drug\Indication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IndicationController extends Controller
{

    public function get(){
        $indications = Indication::get();
        return $this->success($indications);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:indications,name',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        Indication::create([
            'name' => $request->name
        ]);

        return $this->success();
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:indications',
            'name' => 'required|string|max:50|unique:indications,name,' . $request->id,
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $indication = Indication::where('id',$request->id)->first();
        $indication->update([
            'name' => $request->name
        ]);

        return $this->success();
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:indications',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        Indication::where('id',$request->id)->first()->delete();
        return $this->success();
    }

    public function getDrugs(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:indications',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $drugs = Indication::with(['drugs'=>function($q)
        {return $q->select('drugs.id','brand_name');}])
            ->where('id', $request->id)
            ->select('id')
            ->first();

        return $this->success($drugs);
    }

}
