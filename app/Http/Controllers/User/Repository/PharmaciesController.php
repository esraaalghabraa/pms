<?php

namespace App\Http\Controllers\User\Repository;

use App\Http\Controllers\Controller;
use App\Models\Registration\Pharmacy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PharmaciesController extends Controller
{
    public function getPharmacies(): JsonResponse
    {
        $repositories = Pharmacy::select('id', 'name')->get();
        return $this->success($repositories);
    }

    public function getPharmacy(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:pharmacies'
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());

        $repositories = Pharmacy::where('id', $request->id)
            ->with(['drugs'

            ])->first();
        return $this->success($repositories);
    }
}
