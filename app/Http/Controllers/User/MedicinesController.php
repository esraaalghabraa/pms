<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Drug\AddDrugRequest;
use App\Models\Drug\Drug;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MedicinesController extends Controller
{
    // TODO ADD other gets
    public function getMedicines(): JsonResponse
    {
        try {
            $medicines = Drug::select('id', 'brand_name')->get();
            return $this->success($medicines);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
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
            return $this->success($medicine);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }
}
