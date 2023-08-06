<?php

namespace App\Http\Controllers\User\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\PharmacyBatch;
use App\Models\Transaction\PharmacyStorage;
use App\Models\Transaction\SaleBill;
use App\Models\Transaction\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SaleBillsController extends Controller
{
    // TODO ESRAA
    public function getDailyBills(Request $request){
        $validator =  Validator::make($request->all(),[
            'pharmacy_id' => 'required|exists:pharmacies,id',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        return $this->success(SaleBill::where('pharmacy_id',$request->pharmacy_id)->where('customer_id',0)->with('saleItems')->get());
    }

    public function getCustomerBills(Request $request){
        $validator =  Validator::make($request->all(),[
            'pharmacy_id' => 'required|exists:pharmacies,id',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        return $this->success(SaleBill::where('pharmacy_id',$request->pharmacy_id)->whereNot('customer_id',0)->with('saleItems')->get());
    }


    public function create(Request $request){
        $validator =  Validator::make($request->all(),[
            'date' => 'required',
            "total_Sale_price"=>'required',
            "customer_id"=>'required',
            "pharmacy_id"=>'required|exists:pharmacies,id',
            "sale_items"=>'required'
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        if ($request->customer_id != 0) {
            $validator =  Validator::make($request->all(),[
                "customer_id" => 'required|exists:customers,id',
            ]);
            if ($validator->fails())
                return $this->error($validator->errors()->first());
        }
       $sale_bill = SaleBill::create([
            'date'=>$request->date,
            'total_Sale_price'=>$request->total_Sale_price,
            'customer_id'=>$request->customer_id,
            'pharmacy_id'=>$request->pharmacy_id
        ]);

        $sale_items = json_decode(json_decode($request->sale_items));
        foreach ($sale_items as $sale_item)
        {
            SaleItem::create([
                'pharmacy_batch_id'=>$sale_item->pharmacy_batch_id,
                'sale_bill_id'=>$sale_bill->id,
                'quantity'=>$sale_item->quantity,
                'price'=>$sale_item->price,
                'date'=>$sale_bill->date
            ]);
            $P_batch = PharmacyBatch::where('id',$sale_item->pharmacy_batch_id)->first();
            $P_batch->update(['quantity'=>$P_batch->quantity-$sale_item->quantity]);

            $P_storage = PharmacyStorage::where('id',$P_batch->pharmacy_storage_id)->first();
            $P_storage->update(['quantity'=>$P_batch->quantity-$P_storage->quantity]);

            $P_batch->save();
            $P_storage->save();
        }
        return $this->success();
    }

    public function addSale(Request $request){
        $validator =  Validator::make($request->all(),[
            'id'=>'required|exists:sale_bills',
            "total_Sale_price"=>'required',
            "pharmacy_id"=>'required|exists:pharmacies,id',
            "sale_items"=>'required'
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $sale_bill = SaleBill::where('id',$request->id)->first();
        $sale_items = json_decode(json_decode($request->sale_items));
        foreach ($sale_items as $sale_item)
        {$item_price=0;
           $sale_item = SaleItem::create([
                'pharmacy_batch_id'=>$sale_item->pharmacy_batch_id,
                'sale_bill_id'=>$sale_bill->id,
                'quantity'=>$sale_item->quantity,
                'price'=>$sale_item->price,
                'date'=>$sale_bill->date
            ]);
            $P_batch = PharmacyBatch::where('id',$sale_item->pharmacy_batch_id)->first();
            $P_batch->update(['quantity'=>$P_batch->quantity-$sale_item->quantity]);

            $P_storage = PharmacyStorage::where('id',$P_batch->pharmacy_storage_id)->first();
            $P_storage->update(['quantity'=>$P_batch->quantity-$P_storage->quantity]);

            $P_batch->save();
            $P_storage->save();
            $item_price=$sale_item->quantity*$sale_item->price;
            $sale_bill->update(['total_Sale_price'=>$sale_bill->total_Sale_price+$item_price]);
            $sale_bill->save();
        }
        return $this->success();
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:sale_bills',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $S_bill = SaleBill::with('saleItems')->where('id',$request->id)->first();
        if ($S_bill->saleItems)
            foreach ($S_bill->saleItems as $saleItem)
                $saleItem->delete();
        $S_bill->delete();
        return $this->success();
    }

    public function getBill(Request $request){
        $validator =  Validator::make($request->all(),[
            'id'=>'required|exists:sale_bills',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $sale_bill = SaleBill::with('saleItems')->with('customer')->where('id',$request->id)->first();
        return $this->success($sale_bill);
    }
}
