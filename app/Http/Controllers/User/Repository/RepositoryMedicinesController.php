<?php

namespace App\Http\Controllers\User\Repository;

use App\Http\Controllers\Controller;
use App\Http\Resources\StoredMedicinesResource;
use App\Models\RepositoryBatch;
use App\Models\Transaction\RepositoryStorage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Validator;

class RepositoryMedicinesController extends Controller
{
    // TODO add other get
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
        return $this->success(new StoredMedicinesResource($medicines));
    }

    public function getStoredMedicine(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:repository_storages',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());

        $medicine = RepositoryStorage::with(['drug' => function ($q) {
            return $q->select('id', 'brand_name', 'scientific_name');
        }])->with(['batches'])->find($request->id);
        return $this->success($medicine);
    }

    // TODO fix null value
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
            return $this->success(new StoredMedicinesResource($medicines));
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function updateMedicine(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'medicine_storage_id' => 'required|numeric|exists:repository_storages,id',
            'price' => 'required|numeric',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        RepositoryStorage::where('id', $request->medicine_storage_id)->update([
            'price' => $request->price,
        ]);
        return $this->success();
    }

    public function createBatchMedicine(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'medicine_id' => 'required|numeric|exists:drugs,id',
            'repository_id' => 'required|numeric|exists:repositories,id',
            'price' => 'required|numeric',
            'quantity' => 'required|numeric|min:1',
            'barcode' => 'required|string|min:5|max:15|unique:repository_batches,barcode',
            'expired_date' => 'required|string|max:50',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());

        $repositoryStorage = RepositoryStorage::where([
            'drug_id'=>$request->medicine_id,
            'repository_id'=>$request->repository_id
        ])->get();
        if (count($repositoryStorage) == 0) {
            $repositoryStorage[0] = RepositoryStorage::create([
                'quantity' => $request->quantity,
                'price' => $request->price,
                'drug_id' => $request->medicine_id,
                'repository_id' => $request->repository_id,
            ]);
        }
        $previousBatch = RepositoryBatch::select('id', 'number', 'repository_storage_id')
            ->where('repository_storage_id', $repositoryStorage[0]->id)->latest()->first();

        $batchNumber = 1;
        if ($previousBatch != null) {
            $batchNumber = $previousBatch->number + 1;
            RepositoryStorage::where('id', $repositoryStorage[0]->id)
            ->update([
                'quantity' => $repositoryStorage[0]->quantity + $request->quantity,
                'price' => $request->price,
            ]);
        }

        RepositoryBatch::create([
            'repository_storage_id' => $repositoryStorage[0]->id,
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
