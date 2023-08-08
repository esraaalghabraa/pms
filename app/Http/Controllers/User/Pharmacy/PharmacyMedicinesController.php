<?php

namespace App\Http\Controllers\User\Pharmacy;

use App\Http\Controllers\Controller;
use App\Http\Resources\StoredMedicinesResource;
use App\Models\RepositoryBatch;
use App\Models\Transaction\PharmacyBatch;
use App\Models\Transaction\PharmacyStorage;
use Illuminate\Auth\Access\AuthorizationException;
use App\Models\Transaction\RepositoryStorage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Validator;

class PharmacyMedicinesController extends Controller
{

//    public function __construct()
//    {
//        try {
//            $this->authorize(['drugs-repo','orders-pharma','employee-pharma','bills-pharma','sales-pharma','stock-pharma']);
//
//        } catch (AuthorizationException $e) {
//            return $this->error('unAuthorized',403);
//        }
//    }
    // TODO add other get
    public function getStoredMedicines(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'pharmacy_id' => 'required|exists:pharmacies,id',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());

        $medicines = PharmacyStorage::where('pharmacy_id', $request->pharmacy_id)
            ->with(['drug' => function ($q) {
                return $q->select('id', 'brand_name');
            }])->get();
        return $this->success(new StoredMedicinesResource($medicines));
    }

    public function getStoredMedicine(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:pharmacy_storages',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());

        $medicine = PharmacyStorage::with(['drug' => function ($q) {
            return $q->select('id', 'brand_name', 'scientific_name');
        }])->with(['batches'])->find($request->id);
        return $this->success($medicine);
    }

    // TODO fix null value
    public function searchStoredMedicines(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'pharmacy_id' => 'required|exists:pharmacies,id',
            'brand_name' => 'required|string',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());

        try {
            $medicines = PharmacyStorage::where('pharmacy_id', $request->pharmacy_id)
                ->with(['drug' => function ($q) use ($request) {
                    return $q->where('brand_name', 'LIKE', '%' . $request->brand_name . '%')
                        ->select('id', 'brand_name');
                }])->get();
            if ($medicines == null)
                return $this->error();
            return $this->success(new StoredMedicinesResource($medicines));
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function updateMedicine(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'medicine_storage_id' => 'required|numeric|exists:pharmacy_storages,id',
            'price' => 'required|numeric',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        PharmacyStorage::where('id', $request->medicine_storage_id)->update([
            'price' => $request->price,
        ]);
        return $this->success();
    }

    public function createBatchMedicine(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'medicine_id' => 'required|numeric|exists:drugs,id',
            'pharmacy_id' => 'required|numeric|exists:pharmacies,id',
            'price' => 'required|numeric',
            'quantity' => 'required|numeric|min:1',
            'barcode' => 'required|string|min:5|max:15|unique:pharmacy_batches,barcode',
            'expired_date' => 'required|string|max:50',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());

        $pharmacyStorage = PharmacyStorage::where([
            'drug_id'=> $request->medicine_id,
            'pharmacy_id'=> $request->pharmacy_id
        ])->get();
        if (count($pharmacyStorage) == 0) {
            $pharmacyStorage[0] = PharmacyStorage::create([
                'quantity' => $request->quantity,
                'price' => $request->price,
                'drug_id' => $request->medicine_id,
                'pharmacy_id' => $request->pharmacy_id,
            ]);
        }
        $previousBatch = PharmacyBatch::select('id', 'number', 'pharmacy_storage_id')
            ->where('pharmacy_storage_id', $pharmacyStorage[0]->id)->latest()->first();

        $batchNumber = 1;
        if ($previousBatch != null) {
            $batchNumber = $previousBatch->number + 1;
            PharmacyStorage::where('id', $pharmacyStorage[0]->id)
                ->update([
                    'quantity' => $pharmacyStorage[0]->quantity + $request->quantity,
                    'price' => $request->price,
                ]);
        }

        PharmacyBatch::create([
            'pharmacy_storage_id' => $pharmacyStorage[0]->id,
            'price' => $request->price,
            'number' => $batchNumber,
            'quantity' => $request->quantity,
            'exists_quantity' => $request->quantity,
            'barcode' => $request->barcode,
            'date_of_entry' => Date::now(),
            'expired_date' => $request->expired_date,
        ]);

        return $this->success();
    }

}
