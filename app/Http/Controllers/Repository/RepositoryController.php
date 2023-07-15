<?php

namespace App\Http\Controllers\Repository;

use App\Http\Controllers\Controller;
use App\Models\Drug\AddDrugRequest;
use App\Models\Transaction\DrugRequest;
use App\Models\Transaction\RepositoryBatch;
use App\Models\Transaction\RepositoryStorage;
use App\Models\Transaction\RequestItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RepositoryController extends Controller
{
    public function createRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'repository_id' => 'required|numeric|exists:repositories,id',
            'brand_name' => 'required|string|max:50',
            'scientific_name' => 'required|string|max:50',
            'capacity' => 'required|string|max:50',
            'titer' => 'required|string|max:50',
            'contraindications' => 'required|string',
            'side_effects' => 'required|string',
            'is_prescription' => 'required',
            'category' => 'required|exists:categories,id',
            'dosage_form' => 'required|exists:dosage_forms,id',
            'manufacture_company' => 'required|string',
            'scientific_materials'=> 'required|string',
            'therapeutic_effects'=> 'required|string',
            'indications'=> 'required|string',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        AddDrugRequest::create([
            'repository_id' => $request->repository_id,
            'brand_name' => $request->brand_name,
            'scientific_name' => $request->scientific_name,
            'capacity' => $request->capacity,
            'titer' => $request->scientific_name,
            'side_effects' => $request->side_effects,
            'is_prescription' => $request->is_prescription,
            'contraindications' => $request->contraindications,
            'category' => $request->category,
            'dosage_form' => $request->dosage_form,
            'manufacture_company' => $request->manufacture_company
        ]);
        return $this->success();
    }

    public function createDrugStorage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'drug_id' => 'required|numeric|exists:drugs,id',
            'repository_id' => 'required|numeric|exists:repositories,id',
            'price' => 'required|numeric',
            'quantity' => 'required|numeric',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $drug_storage = RepositoryStorage::create([
            'drug_id' => $request->drug_id,
            'repository_id' => $request->repository_id,
            'price' => $request->price,
            'quantity' => $request->quantity,
        ]);
        return $this->success($drug_storage);
    }

    public function createBatchDrug(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'repository_storage_id' => 'required|numeric|exists:repository_storages,id',
            'barcode' => 'required',
            'batch_number' => 'required',
            'expired_date' => 'required|string|max:50',
            'date_of_entry' => 'required',
            'batch_quantity' => 'required'
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $drug_request = RepositoryBatch::create([
            'repository_storage_id' => $request->repository_storage_id,
            'barcode' => $request->barcode,
            'batch_number' => $request->batch_number,
            'expired_date' => $request->expired_date,
            'date_of_entry' => $request->date_of_entry,
            'batch_quantity' => $request->batch_quantity,
        ]);
        $repo = RepositoryStorage::where('id',$drug_request->repository_storage_id)->first();
        $repo->update(['quantity'=>$repo->quantity+$drug_request->batch_quantity]);
        $repo->save();
        return $this->success($drug_request);
    }
}
