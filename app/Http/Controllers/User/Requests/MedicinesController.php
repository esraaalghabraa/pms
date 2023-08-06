<?php

namespace App\Http\Controllers\User\Requests;

use App\Http\Controllers\Controller;
use App\Models\Drug\AddDrugRequest;
use App\Models\Drug\Drug;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MedicinesController extends Controller
{
    public function createRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'repository_id' => 'required|numeric|exists:repositories,id',
            'drug_name' => 'required|string|max:50',
            'notes' => 'required|string',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        AddDrugRequest::create([
            'repository_id' => $request->repository_id,
            'drug_name' => $request->drug_name,
            'notes' => $request->notes,
        ]);
        return $this->success();
    }

    public function getMedicines(): JsonResponse
    {
        $medicines = Drug::select('id', 'brand_name')->get();
        return $this->success($medicines);
    }

    public function searchMedicines(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'brand_name' => 'required|string',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        try {
            $medicines = Drug::where('brand_name', 'LIKE', '%' . $request->brand_name . '%')
                ->select('id', 'brand_name')->get();
            if ($medicines == null)
                return $this->error();
            return $this->success($medicines);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function getMedicine(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:drugs',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        try {
            $medicine = Drug::with('category')
                ->with('manufactureCompany')
                ->with('indications')
                ->with('scientificMaterials')
                ->with('therapeuticEffects')
                ->find($request->id);
            if ($medicine == null)
                return $this->error();
            return $this->success($medicine);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

}
