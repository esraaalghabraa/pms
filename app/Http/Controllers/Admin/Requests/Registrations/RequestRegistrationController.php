<?php

namespace App\Http\Controllers\Admin\Requests\Registrations;

use App\Http\Controllers\Controller;
use App\Models\Registration\Pharmacy;
use App\Models\Registration\RegistrationRequest;
use App\Models\Registration\Repository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RequestRegistrationController extends Controller
{
    public function get()
    {
        $ordersPharmacy = RegistrationRequest::where('type','pharmacy')->get();
        $ordersRepository = RegistrationRequest::where('type','repository')->get();
        $data['Pharmacies']=$ordersPharmacy;
        $data['Repositories']=$ordersRepository;
        return $this->success($data);
    }

    public function getPending()
    {
        $ordersPharmacy = RegistrationRequest::where(['type'=>'pharmacy','status'=>'pending'])->get();
        $ordersRepository = RegistrationRequest::where(['type'=>'repository','status'=>'pending'])->get();
        $data['Pharmacies']=$ordersPharmacy;
        $data['Repositories']=$ordersRepository;
        return $this->success($data);
    }

    public function getAccepting()
    {
        $ordersPharmacy = RegistrationRequest::where(['type'=>'pharmacy','status'=>'accepting'])->get();
        $ordersRepository = RegistrationRequest::where(['type'=>'repository','status'=>'accepting'])->get();
        $data['Pharmacies']=$ordersPharmacy;
        $data['Repositories']=$ordersRepository;
        return $this->success($data);
    }

    public function getRejecting()
    {
        $ordersPharmacy = RegistrationRequest::where(['type'=>'pharmacy','status'=>'rejecting'])->get();
        $ordersRepository = RegistrationRequest::where(['type'=>'repository','status'=>'rejecting'])->get();
        $data['Pharmacies']=$ordersPharmacy;
        $data['Repositories']=$ordersRepository;
        return $this->success($data);
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
            if (!$order)
                return $this->error();
            if ($order->type == 'pharmacy'){
                Pharmacy::create([
                    'name' => $order->name,
                    'address' => $order->address,
                    'phone_number' => $order->phone_number,
                    'owner_id' => $order->owner_id,
                ]);
            }else{
                Repository::create([
                    'name' => $order->name,
                    'address' => $order->address,
                    'phone_number' => $order->phone_number,
                    'owner_id' => $order->owner_id,
                ]);
            }
            $order->update(['status'=>'accepting']);
            $order->save();
            DB::commit();
            return $this->success('Done Accept Your Request');
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
        $order->update(['status'=>'rejecting']);
        $order->save();
        return $this->success('Sorry, your account within PMS has been canceled due to fake information within the registration account');
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|exists:registration_requests,id',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        RegistrationRequest::onlyTrashed()->where('id', $request->id)->first()->forceDelete();
        return $this->success();
    }

    public function deleteAll(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'archive_requests' => 'required',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $archiveRequests = json_decode($request->archive_requests);
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

    public function getArchived()
    {
        $orders = RegistrationRequest::onlyTrashed()->get();

        if (!$orders)
            return $this->error('are no archived requests');
        return $this->success($orders);
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
