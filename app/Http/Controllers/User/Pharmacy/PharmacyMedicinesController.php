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
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PharmacyMedicinesController extends Controller
{

    // TODO add other get
    public function getStoredMedicines(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'pharmacy_id' => 'required|exists:pharmacies,id',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());

        try {
            $medicines = PharmacyStorage::where('pharmacy_id', $request->pharmacy_id)
                ->with(['drug' => function ($q) {
                    return $q->select('id', 'brand_name');
                }])->get();
            return $this->success(new StoredMedicinesResource($medicines));
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function getMedicinesOfCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pharmacy_id' => 'required|exists:pharmacies,id',
            'category_id' => 'required|exists:categories,id',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());

        try {
            $medicines = PharmacyStorage::where('pharmacy_id', $request->pharmacy_id)
                ->with(['drug' => function ($q) use ($request) {
                    return $q->select('id', 'category_id', 'brand_name')->where('category_id', $request->category_id);
                }])->get();
            return $this->success($this->getMedicinesFromArray($medicines));
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function getMedicinesOfManufactureCompany(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pharmacy_id' => 'required|exists:pharmacies,id',
            'manufacture_company_id' => 'required|exists:manufacture_companies,id',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());

        try {
            $medicines = PharmacyStorage::where('pharmacy_id', $request->pharmacy_id)
                ->with(['drug' => function ($q) use ($request) {
                    return $q->select('id', 'category_id', 'brand_name')->where('manufacture_company_id', $request->manufacture_company_id);
                }])->get();
            return $this->success($this->getMedicinesFromArray($medicines));
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function getStoredMedicine(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:pharmacy_storages',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());

        try {
            $medicine = PharmacyStorage::with(['drug' => function ($q) {
                return $q->select('id', 'brand_name', 'scientific_name');
            }])->with(['batches'])->find($request->id);
            return $this->success($medicine);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function searchStoredMedicines(Request $request)
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
            return $this->success($this->getMedicinesFromArray($medicines));
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
        try {
            PharmacyStorage::where('id', $request->medicine_storage_id)->update([
                'price' => $request->price,
            ]);
            return $this->success();
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    // TODO FIX UNIQUE BARCODE
    public function createBatchMedicine(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'medicine_id' => 'required|numeric|exists:drugs,id',
            'pharmacy_id' => 'required|numeric|exists:pharmacies,id',
            'price' => 'required|numeric',
            'quantity' => 'required|numeric|min:1',
            'barcode' => 'required|string|min:5|max:15',
            'expired_date' => 'required|string|max:50',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());

        try {
            DB::beginTransaction();
            $pharmacyStorage = PharmacyStorage::where([
                'drug_id' => $request->medicine_id,
                'pharmacy_id' => $request->pharmacy_id
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
            DB::commit();
            return $this->success();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage());
        }
    }

    public function getMedicinesFromArray($medicines): array
    {
        $medicines=$medicines->map(function ($medicine) {
            return [
                'id' => $medicine->id,
                'quantity' => $medicine->quantity,
                'price' => $medicine->price,
                'brand_name' => $medicine->drug != null ? $medicine->drug->brand_name : '',
            ];
        });
        $resultMedicines = [];
        $i = 0;
        foreach ($medicines as $medicine) {
            if ($medicine['brand_name'] != '')
                $resultMedicines[$i++] = $medicine;
        }
        return $resultMedicines;
    }

}
