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
            'brand_name' => 'required|string|max:50',
            'scientific_name' => 'required|string|max:50',
            'capacity' => 'required|string|max:50',
            'titer' => 'required|string|max:50',
            'contraindications' => 'required|string',
            'side_effects' => 'required|string',
            'is_prescription' => 'required',
            'category' => 'required|exists:categories,id',
            'dosage_form' => 'required|exists:dosage_forms,id',
            'manufacture_company' => 'required|string',
            'scientific_materials' => 'required|string',
            'therapeutic_effects' => 'required|string',
            'indications' => 'required|string',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        AddDrugRequest::create([
            'repository_id' => $request->repository_id,
            'brand_name' => $request->brand_name,
            'scientific_name' => $request->scientific_name,
            'capacity' => $request->capacity,
            'titer' => $request->scientific_name,
            'side_effects' => $request->side_effects,
            'is_prescription' => $request->is_prescription,
            'contraindications' => $request->contraindications,
            'category' => $request->category,
            'dosage_form' => $request->dosage_form,
            'manufacture_company' => $request->manufacture_company
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
