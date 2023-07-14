<?php

namespace App\Http\Controllers\User\Repository;

use App\Http\Controllers\Controller;
use App\Models\RepositoryBatch;
use App\Models\Transaction\RepositoryStorage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RepositoryMedicinesController extends Controller
{
    public function getStoredMedicines(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'repository_id' => 'required|exists:repositories,id',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());

        $medicines = RepositoryStorage::where('repository_id', $request->repository_id)
            ->with(['drug' => function ($q) {
                return $q->select('id', 'brand_name');
            }])->get();

        $formattedMedicines = $medicines->map(function ($medicine) {
            return [
                'id' => $medicine->id,
                'quantity' => $medicine->quantity,
                'price' => $medicine->price,
                'brand_name' => $medicine->drug->brand_name,
            ];
        });
        return $this->success($formattedMedicines);
    }

    public function searchStoredMedicines(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'repository_id' => 'required|exists:repositories,id',
            'brand_name' => 'required|string',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());

        try {
            $medicines = RepositoryStorage::where('repository_id', $request->repository_id)
                ->with(['drug' => function ($q) use ($request) {
                    return $q->where('brand_name', 'LIKE', '%' . $request->brand_name . '%')
                        ->select('id', 'brand_name');
                }])->get();
            if ($medicines == null)
                return $this->error();

            $formattedMedicines = $medicines->map(function ($medicine) {
                return [
                    'id' => $medicine->id,
                    'quantity' => $medicine->quantity,
                    'price' => $medicine->price,
                    'brand_name' => $medicine->drug->brand_name,
                ];
            });
            return $this->success($formattedMedicines);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function createMedicineStorage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'drug_id' => 'required|numeric|exists:drugs,id',
            'repository_id' => 'required|numeric|exists:repositories,id',
            'price' => 'required|numeric',
            'quantity' => 'required|numeric',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());

        $drug_storage=RepositoryStorage::where('drug_id',$request->drug_id)->where('repository_id',$request->repository_id)->get();
        if(count($drug_storage)>0){
            return $this->error('Medicine storage already exists');
        }

        RepositoryStorage::create([
            'drug_id' => $request->drug_id,
            'repository_id' => $request->repository_id,
            'price' => $request->price,
            'quantity' => $request->quantity,
        ]);
        return $this->success();
    }

    public function updateMedicine(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'medicine_storage_id' => 'required|numeric|exists:repository_storages,id',
            'price' => 'required|numeric',
            'quantity' => 'required|numeric',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        RepositoryStorage::where('id', $request->medicine_storage_id)->update([
            'price' => $request->price,
            'quantity' => $request->quantity,
        ]);
        return $this->success();
    }

    public function createBatchMedicine(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'medicine_storage_id' => 'required|numeric|exists:repository_storages,id',
            'price' => 'required|numeric',
            'quantity' => 'required|numeric|min:1',
            'barcode' => 'required|string|min:5|max:15',
            'date_of_entry' => 'required|string|max:50',
            'expired_date' => 'required|string|max:50',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());

        $previousBatch = RepositoryBatch::select('id', 'number', 'repository_storage_id')
            ->where('repository_storage_id', $request->medicine_storage_id)->latest()->first();

        $batchNumber = 0;
        if ($previousBatch != null) {
            $batchNumber = $previousBatch->number + 1;
        }

        RepositoryBatch::create([
            'repository_storage_id' => $request->medicine_storage_id,
            'price' => $request->price,
            'number' => $batchNumber,
            'quantity' => $request->quantity,
            'barcode' => $request->barcode,
//            'date_of_entry' => $request->date_of_entry,
            'expired_date' => $request->expired_date,
        ]);

        $medicine_storage = RepositoryStorage::where('id', $request->medicine_storage_id)->first();
        $medicine_storage->update([
            'quantity' => $medicine_storage->quantity + $request->quantity,
            'price' => $request->price
        ]);
        $medicine_storage->save();
        return $this->success();
    }

}
