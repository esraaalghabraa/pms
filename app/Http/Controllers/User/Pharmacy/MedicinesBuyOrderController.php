<?php

namespace App\Http\Controllers\User\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\ItemBatch;
use App\Models\Registration\Repository;
use App\Models\RepositoryBatch;
use App\Models\Transaction\DrugRequest;
use App\Models\Transaction\PharmacyBatch;
use App\Models\Transaction\PharmacyStorage;
use App\Models\Transaction\RepositoryStorage;
use App\Models\Transaction\RequestItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Validator;

class MedicinesBuyOrderController extends Controller
{
    public function getRepositories(): JsonResponse
    {
        try {
            $repositories = Repository::select('id', 'name')->get();
            return $this->success($repositories);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }

    }

    public function searchRepository(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        try {
            $repositories = Repository::where('name', 'LIKE', '%' . $request->name . '%')
                ->select('id', 'name')->get();
            return $this->success($repositories);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function getRepositoryWithRequests(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:repositories'
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());

        try {
            $repositories = Repository::select('id', 'name', 'phone_number', 'address')
                ->with(['drugRequests'])->find($request->id);
            return $this->success($repositories);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function getRepositoryWithMedicines(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:repositories'
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());

        try {
            $repositories = Repository::select('id', 'name', 'phone_number', 'address')
                ->with(['RepositoryStorages'])->find($request->id);
            return $this->success($repositories);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function get(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'pharmacy_id' => 'required|exists:pharmacies,id',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());

        try {
            $medicineRequests = DrugRequest::where('pharmacy_id', $request->pharmacy_id)
                ->with(['repository' => function ($q) {
                    return $q->select('id', 'name');
                }])->get();
            $formattedMedicineRequests = $medicineRequests->map(function ($medicineRequest) {
                return [
                    'id' => $medicineRequest->id,
                    'status' => $medicineRequest->status,
                    'date' => $medicineRequest->date,
                    'date_delivery' => $medicineRequest->date_delivery != null ?
                        $medicineRequest->date_delivery : "Undetected",
                    'repository_name' => $medicineRequest->repository->name,
                ];
            });
            return $this->success($formattedMedicineRequests);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function getMedicinesOrder(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:drug_requests',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());

        try {
            $medicineRequest = DrugRequest::where('id', $request->id)
                ->with(['requestItems' => function ($q) {
                    return $q->with(['batches' => function ($q) {
                        return $q->select('repository_batches.id', 'barcode', 'expired_date');
                    }]);
                }])
                ->with(['repository' => function ($q) {
                    return $q->select('id', 'name');
                }])->first();
            $requestItems = $medicineRequest->requestItems->map(function ($requestItem) {
                $batches = $requestItem->batches->map(function ($batch) use ($requestItem) {
                    $quantity = ItemBatch::where('item_id', $requestItem->id)->where('batch_id', $batch->id)
                        ->first()->quantity;
                    return [
                        'id' => $batch->id,
                        'barcode' => $batch->barcode,
                        'expired_date' => $batch->expired_date,
                        'quantity' => $quantity,
                    ];
                });
                return [
                    'id' => $requestItem->id,
                    'quantity' => $requestItem->quantity,
                    'price' => $requestItem->id,
                    'repository_storage_id' => $requestItem->id,
                    'batches' => $batches,
                ];
            });
            $medicineRequest = [
                'id' => $medicineRequest->id,
                'status' => $medicineRequest->status,
                'date' => $medicineRequest->date,
                'date_delivery' => $medicineRequest->date_delivery != null ?
                    $medicineRequest->date_delivery : "Undetected",
                'repository_name' => $medicineRequest->repository->name,
                'request_items' => $requestItems,
            ];

            return $this->success($medicineRequest);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function sendOrder(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'pharmacy_id' => 'required|exists:pharmacies,id',
            'repository_id' => 'required|exists:repositories,id',
            'order_items' => 'required',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        try {
            $medicineRequest = DrugRequest::create([
                'pharmacy_id' => $request->pharmacy_id,
                'repository_id' => $request->repository_id,
            ]);
            $orderItems = json_decode(json_decode($request->order_items));
            foreach ($orderItems as $medicine) {
                $repositoryStorage = RepositoryStorage::where('id', $medicine->repository_storage_id)->first();
                if ($medicine->quantity > $repositoryStorage->quantity)
                    return $this->error('quantity not available');
                $repositoryStorage->quantity -= $medicine->quantity;
                $repositoryStorage->save();
                $item = RequestItem::create([
                    'quantity' => $medicine->quantity,
                    'price' => $repositoryStorage->price,
                    'repository_storage_id' => $medicine->repository_storage_id,
                    'drug_request_id' => $medicineRequest->id,
                ]);
                $batches = RepositoryBatch::where('repository_storage_id', $medicine->repository_storage_id)
                    ->whereNot('exists_quantity', 0)->orderby('expired_date')->get();
                $quantity = $medicine->quantity;

                foreach ($batches as $batch) {
                    if ($quantity == 0)
                        break;
                    if ($quantity >= $batch->exists_quantity) {
                        $quantityOfBatch = $batch->exists_quantity;
                        $quantity -= $quantityOfBatch;
                    } else {
                        $quantityOfBatch = $quantity;
                        $quantity = 0;
                    }
                    RepositoryBatch::where('id', $batch->id)
                        ->update([
                            'exists_quantity' => $batch->exists_quantity - $quantityOfBatch
                        ]);
                    ItemBatch::create([
                        'item_id' => $item->id,
                        'batch_id' => $batch->id,
                        'quantity' => $quantityOfBatch,
                    ]);
                }
            }
            return $this->success();
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function receive(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:drug_requests',
            'pharmacy_id' => 'required|exists:pharmacies,id',
            'request_items_prices' => 'required'
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        try {
            $medicineRequest = DrugRequest::where('id', $request->id)->first();
            if ($medicineRequest->status != 'accepting') {
                return $this->error("you can't receipt Order because status is " . $medicineRequest->status);
            }
            $medicineRequest->status = 'received';
            $request_items_prices = json_decode(json_decode($request->request_items_prices));
            foreach ($request_items_prices as $requestItemPrice) {
                $requestItem = RequestItem::find($requestItemPrice->id);
                $pharmacyStorage = PharmacyStorage::where([
                    'drug_id' => $requestItem->repositoryStorage->drug->id,
                    'pharmacy_id' => $request->pharmacy_id,
                ])->first();
                if (!$pharmacyStorage) {
                    $pharmacyStorage = PharmacyStorage::create([
                        'pharmacy_id' => $request->pharmacy_id,
                        'drug_id' => $requestItem->repositoryStorage->drug->id,
                        'price' => $requestItemPrice->price,
                        'quantity' => $requestItem->quantity,
                    ]);
                } else {
                    $pharmacyStorage->update([
                        'price' => $requestItemPrice->price,
                        'quantity' => $pharmacyStorage->quantity + $requestItem->quantity,
                    ]);
                }
                foreach ($requestItem->batches as $batch) {
                    $itemBatch = ItemBatch::where([
                        'item_id' => $requestItem->id,
                        'batch_id' => $batch->id,
                    ])->first();
                    $pharmacyBatchAlready = PharmacyBatch::where([
                        'pharmacy_storage_id' => $pharmacyStorage->id,
                        'barcode' => $batch->barcode,
                    ])->first();
                    if (!$pharmacyBatchAlready) {
                        $pharmacyBatch = PharmacyBatch::where('pharmacy_storage_id', $pharmacyStorage->id)->latest()->first();
                        $batchNumber = $pharmacyBatch ? $pharmacyBatch->number + 1 : 1;
                        PharmacyBatch::create([
                            'pharmacy_storage_id' => $pharmacyStorage->id,
                            'price' => $requestItemPrice->price,
                            'number' => $batchNumber,
                            'quantity' => $itemBatch->quantity,
                            'exists_quantity' => $itemBatch->quantity,
                            'barcode' => $batch->barcode,
                            'date_of_entry' => Date::now(),
                            'expired_date' => $batch->expired_date,
                        ]);
                    } else {
                        PharmacyBatch::where('id', $pharmacyBatchAlready->id)->update([
                            'price' => $requestItemPrice->price,
                            'quantity' => $pharmacyBatchAlready->quantity + $itemBatch->quantity,
                            'exists_quantity' => $pharmacyBatchAlready->exists_quantity + $itemBatch->quantity,
                            'date_of_entry' => Date::now(),
                        ]);
                    }
                }
            }
            return $this->success();
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

}
