<?php

namespace App\Http\Controllers\User\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\Transaction\DrugRequest;
use App\Models\Transaction\RequestItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Validator;

class MedicinesBuyOrderController extends Controller
{
    public function sendOrder(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'pharmacy_id' => 'required|exists:pharmacies,id',
            'repository_id' => 'required|exists:repositories,id',
            'selected_medicines' => 'required',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        try {
            $medicineRequest = DrugRequest::create([
                'status' => 'pending',
                'date' => Date::now(), // TODO remove after migrate
                'buy_bill_id' => 0,
                'pharmacy_id' => $request->pharmacy_id,
                'repository_id' => $request->repository_id,
            ]);
            $selectedMedicines = json_decode(json_decode($request->selected_medicines));
            foreach ($selectedMedicines as $medicine)
                RequestItem::create([
                    'quantity' => $medicine->quantity,
                    'price' => $medicine->price,
                    'repository_storage_id' => $medicine->id,
                    'drug_request_id' => $medicineRequest->id,
                ]);
            return $this->success();
        } catch (\Exception $e) {
            return $this->error($e);
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
            $medicineRequests = DrugRequest::where('id', $request->id)->with('requestItems')
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
                    'request_items' => $medicineRequest->requestItems,
                ];
            });
            return $this->success($formattedMedicineRequests);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

}
