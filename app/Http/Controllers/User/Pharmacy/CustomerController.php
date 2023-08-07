<?php

namespace App\Http\Controllers\User\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\PharmacyCustomer;
use App\Models\Registration\Pharmacy;
use App\Models\Transaction\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function getAll(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pharmacy_id' => 'required|exists:pharmacies,id',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $customers = Pharmacy::with(['customers' => function ($q) {
            return $q->with('saleBills');
        }])->find($request->pharmacy_id)->customers;
        $result_customers = $customers->map(function ($customer) {
            return [
                'id' => $customer->id,
                'name' => $customer->name,
                'phone' => $customer->phone,
                'address' => $customer->address,
                'sale_bills_count' => count($customer->saleBills),
            ];
        });
        return $this->success($result_customers);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pharmacy_id' => 'required|exists:pharmacies,id',
            'name' => 'required|string|max:50',
            "phone" => 'required|string|max:50',
            "address" => 'required|string'
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $customer = Customer::where([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address
        ])->first();
        if (!$customer) {
            $customer = Customer::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address
            ]);
        }
        $pharmacy_customer = PharmacyCustomer::where([
            'customer_id' => $customer->id,
            'pharmacy_id' => $request->pharmacy_id
        ])->first();
        if (!$pharmacy_customer) {
            PharmacyCustomer::create([
                'customer_id' => $customer->id,
                'pharmacy_id' => $request->pharmacy_id
            ]);
            return $this->success();
        }
        return $this->error('customer already exist');
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:customers',
            'name' => 'required|string|max:50',
            "phone" => 'required|string|max:50',
            "address" => 'required|string'
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $customer = Customer::where('id', $request->id)->first();
        $customer->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);
        return $this->success();
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:customers',
            'pharmacy_id' => 'required|exists:pharmacies,id',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        PharmacyCustomer::where([
            'customer_id' => $request->id,
            'pharmacy_id' => $request->pharmacy_id,
        ])->delete();
        $pharmacy_customer = PharmacyCustomer::where([
            'customer_id' => $request->id,
        ])->get();
        if (count($pharmacy_customer) == 0) {
            Customer::where('id', $request->id)->delete();
        }
        return $this->success();
    }

    public function getCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:customers',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $customer = Customer::with('saleBills')->where('id', $request->id)->first();
        return $this->success($customer);
    }

}
