<?php

namespace App\Http\Controllers\Repository;

use App\Http\Controllers\Controller;
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
            'pharmacy_id' => 'required|numeric|exists:pharmacies,id',
            'items' => 'required',
            'status' => 'required|string|max:50',
            'date' => 'required'
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $drug_request = DrugRequest::create([
            'repository_id' => $request->repository_id,
            'pharmacy_id' => $request->pharmacy_id,
            'status' => $request->status,
            'date' => $request->date,
        ]);
        $items = json_decode($request->items);

        foreach ($items as $item) {
            RequestItem::create([
                'repository_storage_id' => $item->repository_storage_id,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'drug_request_id' => $drug_request->id,
            ]);
        }
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
