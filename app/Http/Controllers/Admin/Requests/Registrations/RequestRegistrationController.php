<?php

namespace App\Http\Controllers\Admin\Orders\Registrations;

use App\Http\Controllers\Controller;
use App\Models\Registration\RegistrationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RequestRegistrationController extends Controller
{
    public function getRequests()
    {
        $orders = RegistrationRequest::get();
        return $this->success($orders);
    }

    public function createRequest(Request $request)
    {
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
            RegistrationRequest::create([
                'name' => $request->name,
                'type' => $request->type,
                'status' => $request->status,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'owner_id' => auth()->user()->id,
            ]);
            DB::commit();
            return $this->success();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e);
        }
    }

    public function rejectRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|exists:registration_requests,id',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        RegistrationRequest::where('id', $request->id)->first()->delete();
        return $this->error('Sorry, your account within PMS has been canceled due to fake information within the registration account');
    }

    public function acceptRequest(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|numeric|exists:registration_requests,id',
            ]);

            if ($validator->fails())
                return $this->error($validator->errors()->first());
            $order = RegistrationRequest::where('id', $request->id)->first();
            if (!$order)
                return $this->error();
            Pharmacy::create([
                'name' => $order->name,
                'address' => $order->address,
                'phone_number' => $order->phone_number,
                'owner_id' => $order->owner_id,
            ]);
            $order->delete();
            DB::commit();
            return $this->success();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e);
        }
    }

    public function deleteRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|exists:registration_requests,id',
        ]);

        if ($validator->fails())
            return $this->error($validator->errors()->first());
        RegistrationRequest::where('id', $request->id)->forceDelete();
        return $this->success();
    }

    public function archiveRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|exists:registration_requests,id',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        RegistrationRequest::where('id', $request->id)->first()->delete();
        return $this->success();
    }

    public function removeArchiveRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|exists:registration_requests,id',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        RegistrationRequest::where('id', $request->id)->first()->update(['deleted_at' => null])->save();
        return $this->success();
    }

    public function getArchivedRequests(Request $request)
    {
        $orders = RegistrationRequest::onlyTrashed()->get();

        if (!$orders)
            return $this->error('are no archived requests');
        return $this->success($orders);
    }


}
