<?php

namespace App\Http\Controllers\Admin\Requests\Drugs;

use App\Http\Controllers\Admin\Drugs\DrugController;
use App\Http\Controllers\Controller;
use App\Models\AddDrugRequest;
use App\Models\Drug\Drug;
use App\Models\Registration\Pharmacy;
use App\Models\Registration\RegistrationRequest;
use App\Models\Registration\Repository;
use App\Models\Transaction\DrugRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class RequestDrugController extends Controller
{

    public function get()
    {
       return $this->success(AddDrugRequest::get());
    }

    public function getPending()
    {
        $orders = AddDrugRequest::where('status','pending')->get();
        return $this->success($orders);
    }

    public function getAccepting()
    {
        $orders = AddDrugRequest::where('status','accepting')->get();
        return $this->success($orders);
    }

    public function getRejecting()
    {
        $orders = AddDrugRequest::where('status','rejecting')->get();
        return $this->success($orders);
    }

    public function accept(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|numeric|exists:add_drug_requests,id',
            ]);

            if ($validator->fails())
                return $this->error($validator->errors()->first());
            $order = AddDrugRequest::where('id', $request->id)->first();
            if (!$order)
                return $this->error();
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
            'id' => 'required|numeric|exists:add_drug_requests,id',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $order = AddDrugRequest::where('id', $request->id)->first();
        $order->update(['status'=>'rejecting']);
        $order->save();
        return $this->success('Sorry, your account within PMS has been canceled due to fake information within the registration account');
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|exists:add_drug_requests,id',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        AddDrugRequest::onlyTrashed()->where('id', $request->id)->first()->forceDelete();
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
        foreach ($archiveRequests as $archiveRequest)
            AddDrugRequest::onlyTrashed()
                ->where('id', $archiveRequest->id)
                ->first()->forceDelete();

        return $this->success();
    }

    public function getArchived()
    {
        $orders = AddDrugRequest::onlyTrashed()->get();

        if (!$orders)
            return $this->error('are no archived requests');
        return $this->success($orders);
    }

    public function addToArchived(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|exists:add_drug_requests,id',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        AddDrugRequest::where('id', $request->id)->first()->delete();
        return $this->success();
    }

    public function returnFromArchived(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|exists:add_drug_requests,id',
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
