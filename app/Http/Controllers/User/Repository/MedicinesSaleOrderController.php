<?php

namespace App\Http\Controllers\User\Repository;

use App\Http\Controllers\Controller;
use App\Models\ItemBatch;
use App\Models\Registration\Pharmacy;
use App\Models\Transaction\DrugRequest;
use App\Models\Transaction\RequestItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Validator;

class MedicinesSaleOrderController extends Controller
{
    public function getPharmacies(): JsonResponse
    {
        $pharmacies = Pharmacy::select('id', 'name')->get();
        return $this->success($pharmacies);
    }

    public function getPharmacy(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:pharmacies'
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());

        $repositories = Pharmacy::select('id', 'name', 'phone_number', 'address')
            ->with(['drugRequests'])->find($request->id);
        return $this->success($repositories);
    }

    public function getMedicinesOrders(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'repository_id' => 'required|exists:pharmacies,id',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());

        try {
            $medicineRequests = DrugRequest::where('repository_id', $request->repository_id)
                ->with(['pharmacy' => function ($q) {
                    return $q->select('id', 'name');
                }])->get();
            $formattedMedicineRequests = $medicineRequests->map(function ($medicineRequest) {
                return [
                    'id' => $medicineRequest->id,
                    'status' => $medicineRequest->status,
                    'date' => $medicineRequest->date,
                    'date_delivery' => $medicineRequest->date_delivery != null ?
                        $medicineRequest->date_delivery : "Undetected",
                    'pharmacy_name' => $medicineRequest->pharmacy->name,
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
                ->with(['pharmacy' => function ($q) {
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
                'pharmacy_name' => $medicineRequest->pharmacy->name,
                'request_items' => $requestItems,
            ];

            return $this->success($medicineRequest);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function acceptOrder(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:drug_requests',
            'date_delivery' => 'required',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        try {
            DrugRequest::where('id', $request->id)->update([
                'status' => 'accepting',
                'date_delivery' => $request->date_delivery,
            ]);
            return $this->success();
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function rejectOrder(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:drug_requests',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        try {
            DrugRequest::where('id', $request->id)->update([
                'status' => 'rejecting',
            ]);
            return $this->success();
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }


}
