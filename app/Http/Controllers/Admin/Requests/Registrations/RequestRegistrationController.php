<?php

namespace App\Http\Controllers\Admin\Requests\Registrations;

use App\Http\Controllers\Controller;
use App\Models\Registration\Pharmacy;
use App\Models\Registration\PharmacyUser;
use App\Models\Registration\RegistrationRequest;
use App\Models\Registration\Repository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RequestRegistrationController extends Controller
{
    public function get()
    {
        $orders = RegistrationRequest::get();
        return $this->success($orders);
    }

    public function getPending()
    {
        $orders = RegistrationRequest::where('status','pending')->get();
        return $this->success($orders);
    }

    public function getAccepting()
    {
        $orders = RegistrationRequest::where('status','accepting')->get();
        return $this->success($orders);
    }

    public function getRejecting()
    {
        $orders = RegistrationRequest::where('status','rejecting')->get();
        return $this->success($orders);
    }

    public function getArchived()
    {
        $orders = RegistrationRequest::onlyTrashed()->get();

        return $this->success($orders);
    }

    public function accept(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|numeric|exists:registration_requests,id',
            ]);

            if ($validator->fails())
                return $this->error($validator->errors()->first());
            $order = RegistrationRequest::where('id', $request->id)->first();
            if ($order->status!='pending')
                return $this->error('you can not accept Order because its status is ' .$order->status);
            if ($order->type == 'pharmacy'){
               $pharmacy = Pharmacy::create([
                    'name' => $order->name,
                    'address' => $order->address,
                    'phone_number' => $order->phone_number,
                ]);
                PharmacyUser::create([
                   'user_id' =>Auth::user()->id,
                    'pharmacy_id'=>$pharmacy->id
                ]);
            }else{
                Repository::create([
                    'name' => $order->name,
                    'address' => $order->address,
                    'phone_number' => $order->phone_number,
                    'user_id' => $order->user_id,
                ]);
            }
            $order->update(['status'=>'accepting']);
            $order->save();
            DB::commit();
            return $this->success();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e);
        }
    }

    public function reject(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|exists:registration_requests,id',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $order = RegistrationRequest::where('id', $request->id)->first();
        if ($order->status!='pending')
            return $this->error('you can not reject Order because its status is ' .$order->status);
        $order->update(['status'=>'rejecting']);
        $order->save();
        return $this->success();
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|exists:registration_requests,id',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $order =    RegistrationRequest::onlyTrashed()->where('id', $request->id)->first();
        if (!$order)
            return $this->error('you must archive request before delete it');
        $order->forceDelete();
        $order->save();
        return $this->success();
    }

    public function deleteAll(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'archive_requests_ids' => 'required',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $archiveRequests = json_decode(json_decode($request->archive_requests_ids));
        foreach ($archiveRequests as $archiveRequest) {
            $Request = RegistrationRequest::onlyTrashed()
                ->where('id', $archiveRequest->id)
                ->first();
                if ($Request)
                    $Request->forceDelete();
                else
                    return $this->error('request not found');
        }

        return $this->success();
    }

    public function addToArchived(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|exists:registration_requests,id',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        RegistrationRequest::where('id', $request->id)->first()->delete();
        return $this->success();
    }

    public function returnFromArchived(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|exists:registration_requests,id',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $order = RegistrationRequest::onlyTrashed()->where('id', $request->id)->first();
        if (!$order)
            return $this->error('order not found in archive');
        $order->update(['deleted_at'=>null]);
        $order->save();
        return $this->success();
    }

}
