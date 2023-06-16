<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\Drug\Category;
use App\Models\Drug\Drug;
use App\Models\Transaction\DrugRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PharmacyController extends Controller
{
    public function createCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        Category::create([
            'name' => $request->name
        ]);
        return $this->success();
    }

    public function createDrug(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'unit' => 'required|string|max:50',
            'dosage_form' => 'required|string|max:50',
            'manufacture_company' => 'required|string|max:50',
            'price' => 'required',
            'category_id' => 'required'
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        Drug::create([
            'name' => $request->name,
            'unit' => $request->unit,
            'dosage_form' => $request->dosage_form,
            'manufacture_company' => $request->manufacture_company,
            'price' => $request->price,
//            'category_id' => $request->category_id
        ]);
        return $this->success();
    }

    public function createRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|numeric|exists:suppliers,id',
            'items' => 'required',
            'status' => 'required|string|max:50',
            'date' => 'required'
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $drug_request = DrugRequest::create([
            'supplier_id' => $request->supplier_id,
            'owner_id' => $request->owner_id,
            'status' => $request->status,
            'date' => $request->date,
        ]);
        $items = json_decode($request->items);

        foreach ($items as $item) {
            ItemRequest::create([
                'supplier_storage_id' => $item->supplier_storage_id,
                'quantity' => $item->quantity,
                'request_id' => $drug_request->id,
            ]);
        }
        return $this->success();
    }
}
