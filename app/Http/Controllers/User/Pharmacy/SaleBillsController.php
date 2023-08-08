<?php

namespace App\Http\Controllers\User\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\Transaction\PharmacyBatch;
use App\Models\Transaction\PharmacyStorage;
use App\Models\Transaction\SaleBill;
use App\Models\Transaction\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SaleBillsController extends Controller
{
    public function getMedicineByBarcode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'barcode' => 'required|exists:pharmacy_batches,barcode',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());

        $pharmacy_batch = PharmacyBatch::with(['pharmacyStorage' => function ($q) {
            return $q->with(['drug' => function ($q) {
                return $q->select('drugs.id', 'brand_name');
            }]);
        }])->select('id', 'exists_quantity', 'pharmacy_storage_id')->where('barcode', $request->barcode)->first();
        $medicineBatch = [
            'id' => $pharmacy_batch->id,
            'brand_name' => $pharmacy_batch->pharmacyStorage->drug->brand_name,
            'price' => $pharmacy_batch->pharmacyStorage->price,
            'exists_quantity_of_batch' => $pharmacy_batch->exists_quantity,
            'exists_quantity_of_medicine' => $pharmacy_batch->pharmacyStorage->quantity,
        ];
        return $this->success($medicineBatch);
    }

    public function getDailyBills(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pharmacy_id' => 'required|exists:pharmacies,id',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $daily_bill = SaleBill::where([
            'pharmacy_id' => $request->pharmacy_id,
            'customer_id' => 0
        ])->get();
        return $this->success($daily_bill);
    }

    public function getCustomerBills(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pharmacy_id' => 'required|exists:pharmacies,id',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $customer_bills = SaleBill::where('pharmacy_id', $request->pharmacy_id)
            ->whereNot('customer_id', 0)->with('customer')->get();
        $customer_bills = $customer_bills->map(function ($customer_bill) {
            return [
                "id" => $customer_bill->id,
                "number" => $customer_bill->number,
                "date" => $customer_bill->date,
                "total_sale_price" => $customer_bill->total_sale_price,
                "customer_name" => $customer_bill->customer != null ? $customer_bill->customer->name : "customer already deleted",
            ];
        });
        return $this->success($customer_bills);
    }

    public function getDailyBill(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:sale_bills',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $sale_bill = SaleBill::with('saleItems')->where('id', $request->id)->first();
        return $this->success($sale_bill);
    }

    public function getCustomerBill(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:sale_bills',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $sale_bill = SaleBill::with('saleItems')->with('customer')->where('id', $request->id)->first();
        return $this->success($sale_bill);
    }

    public function createCustomerBill(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "pharmacy_id" => 'required|exists:pharmacies,id',
            "customer_id" => 'required|exists:customers,id',
            "total_sale_price" => 'required',
            'date' => 'required',
            "sale_items" => 'required'
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());

        $previous_sale_bill = SaleBill::where('pharmacy_id', $request->pharmacy_id)->latest()->first();
        $bill_number = $previous_sale_bill ? $previous_sale_bill->number + 1 : 1;

        $sale_bill = SaleBill::create([
            'pharmacy_id' => $request->pharmacy_id,
            'customer_id' => $request->customer_id,
            'number' => $bill_number,
            'total_sale_price' => $request->total_sale_price,
            'date' => $request->date,
        ]);

        $sale_items = json_decode(json_decode($request->sale_items));
        foreach ($sale_items as $sale_item) {
            $P_batch = PharmacyBatch::where('id', $sale_item->batch_id)->first();
            if (!$P_batch) {
                SaleBill::where('id', $sale_bill->id)->delete();
                return $this->error('the batch id is invalid');
            }
            if ($sale_item->quantity > $P_batch->exists_quantity) {
                SaleBill::where('id', $sale_bill->id)->delete();
                return $this->error('the batch quantity is not available');
            }
            $P_batch->update(['exists_quantity' => $P_batch->exists_quantity - $sale_item->quantity]);

            $P_storage = PharmacyStorage::where('id', $P_batch->pharmacy_storage_id)->first();
            $P_storage->update(['quantity' => $P_storage->quantity - $sale_item->quantity]);

            SaleItem::create([
                'pharmacy_batch_id' => $sale_item->batch_id,
                'sale_bill_id' => $sale_bill->id,
                'quantity' => $sale_item->quantity,
                'price' => $P_storage->price,
                'date' => $sale_bill->date
            ]);

            $P_batch->save();
            $P_storage->save();
        }
        return $this->success();
    }

    public function addSaleToDailyBill(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "batch_id" => 'required|exists:pharmacy_batches,id',
            "pharmacy_id" => 'required|exists:pharmacies,id',
            "quantity" => 'required|numeric',
            "date" => 'required|date',
            "time" => 'required',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());


        $sale_bill = SaleBill::where([
            'pharmacy_id' => $request->pharmacy_id,
            'customer_id' => 0,
            'date' => $request->date,
        ])->first();
        if (!$sale_bill) {
            $previous_sale_bill = SaleBill::where('pharmacy_id', $request->pharmacy_id)->latest()->first();
            $bill_number = $previous_sale_bill ? $previous_sale_bill->number + 1 : 1;
            $sale_bill = SaleBill::create([
                'pharmacy_id' => $request->pharmacy_id,
                'customer_id' => 0,
                'number' => $bill_number,
                'date' => $request->date,
            ]);
        }
        $P_batch = PharmacyBatch::where('id', $request->batch_id)->first();
        if ($request->quantity > $P_batch->exists_quantity) {
            $bill = SaleBill::where('id', $sale_bill->id)->whereHas('saleItems')->first();
            if (!$bill) {
                SaleBill::where('id', $sale_bill->id)->delete();
            }
            return $this->error('the batch quantity is not available');
        }
        $P_batch->update(['exists_quantity' => $P_batch->exists_quantity - $request->quantity]);

        $P_storage = PharmacyStorage::where('id', $P_batch->pharmacy_storage_id)->first();
        $P_storage->update(['quantity' => $P_storage->quantity - $request->quantity]);

        SaleItem::create([
            'pharmacy_batch_id' => $request->batch_id,
            'sale_bill_id' => $sale_bill->id,
            'quantity' => $request->quantity,
            'price' => $P_storage->price,
            'time' => $request->time
        ]);
        $sale_bill->total_sale_price += $P_storage->price * $request->quantity;
        $sale_bill->save();
        return $this->success();
    }

    public function deleteBill(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:sale_bills',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $S_bill = SaleBill::with('saleItems')->where('id', $request->id)->first();
        if ($S_bill->saleItems)
            foreach ($S_bill->saleItems as $saleItem)
                $saleItem->delete();
        $S_bill->delete();
        return $this->success();
    }

}
