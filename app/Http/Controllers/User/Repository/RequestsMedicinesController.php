<?php

namespace App\Http\Controllers\User\Repository;

use App\Http\Controllers\Controller;
use App\Models\Drug\AddDrugRequest;
use App\Models\Drug\Drug;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RequestsMedicinesController extends Controller
{
    public function get(Request $request)
    {
        $validator = Validator::make($request->all(), [
        'repository_id' => 'required|numeric|exists:repositories,id',
    ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        return $this->success(AddDrugRequest::where('repository_id',$request->repository_id)->get());
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'repository_id' => 'required|numeric|exists:repositories,id',
            'medicine_name' => 'required|string|max:50',
            'notes' => 'required|string',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        AddDrugRequest::create([
            'repository_id' => $request->repository_id,
            'drug_name' => $request->medicine_name,
            'notes' => $request->notes,
        ]);
        return $this->success();
    }
}
