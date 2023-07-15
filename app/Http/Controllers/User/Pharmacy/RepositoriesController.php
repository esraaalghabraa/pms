<?php

namespace App\Http\Controllers\User\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\Registration\Repository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RepositoriesController extends Controller
{
    public function getRepositories(): JsonResponse
    {
        $repositories = Repository::select('id', 'name')->get();
        return $this->success($repositories);
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
            if ($repositories == null)
                return $this->error();
            return $this->success($repositories);
        } catch (\Exception $e) {
            return $this->error($e);
        }
    }

    public function getRepository(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:repositories'
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());

        $repositories = Repository::where('id', $request->id)
            ->with(['medicineStorages' => function ($q) {
                return $q->with(['drug' => function ($q) {
                    return $q->select('id', 'brand_name');
                }]);
            }])->first();
//        $repositories->medicine_storages = new StoredMedicinesResource($repositories->medicineStorages);
        return $this->success($repositories);
    }

}
