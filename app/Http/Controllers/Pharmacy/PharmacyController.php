<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\Transaction\DrugRequest;
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

}
