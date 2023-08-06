<?php

namespace App\Http\Controllers\User\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\PharmacyBatch;
use App\Models\PharmacyCustomer;
use App\Models\Registration\Pharmacy;
use App\Models\Transaction\Customer;
use App\Models\Transaction\PharmacyStorage;
use App\Models\Transaction\SaleBill;
use App\Models\Transaction\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    // TODO ESRAA
    public function getAll(Request $request){
        $validator = Validator::make($request->all(), [
            'pharmacy_id' => 'required|exists:pharmacies,id',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        return $this->success(Pharmacy::with(['customers'=>function($q){
            return $q->with('saleBills');
        }])->find($request->pharmacy_id)->customers);
    }

    public function create(Request $request){
        $validator =  Validator::make($request->all(),[
            'pharmacy_id' => 'required|exists:pharmacies,id',
            'name' => 'required|string|max:50',
            "phone"=>'required|string|max:50'
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
       $customer = Customer::create([
            'name'=>$request->name,
            'phone'=>$request->phone
        ]);
        PharmacyCustomer::create([
            'customer_id'=>$customer->id,
            'pharmacy_id'=>$request->pharmacy_id
        ]);
        return $this->success();
    }

    public function update(Request $request)
    {
        $validator =  Validator::make($request->all(),[
            'id'=>'required|exists:customers',
            'name' => 'required|string|max:50',
            "phone"=>'required|string|max:50'
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $customer = Customer::where('id',$request->id)->first();
        $customer->update([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);
        return $this->success();
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:customers',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        Customer::where('id',$request->id)->first()->delete();
        return $this->success();
    }

    public function getCustomer(Request $request){
        $validator =  Validator::make($request->all(),[
            'id'=>'required|exists:customers',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $customer = Customer::with('saleBills')->where('id',$request->id)->first();
        return $this->success($customer);
    }

}
