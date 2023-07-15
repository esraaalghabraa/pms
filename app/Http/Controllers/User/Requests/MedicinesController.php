<?php

namespace App\Http\Controllers\User\Requests;

use App\Http\Controllers\Controller;
use App\Models\Drug\Drug;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MedicinesController extends Controller
{
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

}
