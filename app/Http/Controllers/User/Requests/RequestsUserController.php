<?php

namespace App\Http\Controllers\User\Requests;

use App\Http\Controllers\Controller;
use App\Models\Registration\RegistrationRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class RequestsUserController extends Controller
{
    public function createRequest(Request $request)
    {
        abort_if(Gate::denies('create-request'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $this->authorize('create-request');
        $validator = Validator::make($request->all(),
            [
                'name' => ['required', 'string', 'min:7', 'max:50', 'unique:registration_requests,name'],
                'address' => ['required', 'string'],
                'type' => ['required', 'string'],
                'status' => ['required', 'string'],
                'phone_number' => ['required', 'string', 'unique:registration_requests,phone_number']
            ]);
        if ($validator->fails())
            return $validator->errors()->first();
        try {
           $request_register = RegistrationRequest::create([
                'name' => $request->name,
                'type' => $request->type,
                'status' => $request->status,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'owner_id' => auth()->user()->id,
            ]);
            DB::commit();
            return $this->success($request_register);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e);
        }
    }

}
