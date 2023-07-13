<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\Drug\Drug;
use App\Models\Transaction\DrugRequest;
use App\Models\Transaction\PharmacyBatch;
use App\Models\Transaction\PharmacyStorage;
use App\Models\Transaction\RequestItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PharmacyController extends Controller
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
            'pharmacy_id' => 'required|numeric|exists:pharmacies,id',
            'price' => 'required|numeric',
            'quantity' => 'required|numeric',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $drug_storage = PharmacyStorage::create([
            'drug_id' => $request->drug_id,
            'pharmacy_id' => $request->pharmacy_id,
            'price' => $request->price,
            'quantity' => $request->quantity,
        ]);
        return $this->success($drug_storage);
    }

    public function createBatchDrug(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pharmacy_storage_id' => 'required|numeric|exists:pharmacy_storages,id',
            'barcode' => 'required',
            'batch_number' => 'required',
            'expired_date' => 'required|string|max:50',
            'date_of_entry' => 'required',
            'batch_quantity' => 'required'
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $batch = PharmacyBatch::create([
            'pharmacy_storage_id' => $request->pharmacy_storage_id,
            'barcode' => $request->barcode,
            'batch_number' => $request->batch_number,
            'expired_date' => $request->expired_date,
            'date_of_entry' => $request->date_of_entry,
            'batch_quantity' => $request->batch_quantity,
        ]);
        $phama = PharmacyStorage::where('id',$batch->pharmacy_storage_id)->first();
        $phama->update(['quantity'=>$phama->quantity+$phama->batch_quantity]);
        $phama->save();
        return $this->success($batch);
    }

    public function searchDrug(Request $request){
        $validator = Validator::make($request->all(), [
           'brand_name' => 'required',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        try {
            $results = Drug::where('brand_name', 'LIKE', '%' . $request->brand_name . '%')->get();
            if ($results == null) return $this->error();
            return $this->success($results);
        }catch (\Exception $e){
            return $this->error();
        }
    }

}
