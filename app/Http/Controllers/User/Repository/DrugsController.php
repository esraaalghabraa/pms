<?php

namespace App\Http\Controllers\User\Repository;

use App\Http\Controllers\Controller;
use App\Models\RepositoryBatch;
use App\Models\Transaction\RepositoryStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DrugsController extends Controller
{
    function addDrugsBatch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'drug_id' => 'required|number|exists:drugs',
            'repository_id' => 'required|number|exists:repositories',
            'quantity' => 'required|number|min:0',
            'price' => 'required|number|min:0',
            'barcode' => 'required|number|min:0',//detect number digits
            'expired_date' => 'required|date',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());

        $drugStore = RepositoryStorage::where('drug_id', $request->drug_id)
            ->andWhere('repository_id',$request->repository_id)->first();
        if (!$drugStore) {
            $drugStore = RepositoryStorage::create([
                'drug_id' => $request->drug_id,
                'repository_id' => $request->repository_id,
                'quantity' => $request->quantity,
                'price' => $request->price,
            ]);
        } else {
            $drugStore->update([
                'quantity' => $drugStore->quantity + $request->quantity,
                'price' => $request->price,
            ]);
            $drugStore->save();
        }
        $repositoryBatch = RepositoryBatch::where('drug_id', $request->drug_id)
            ->andWhere('repository_id',$request->repository_id)->leatest('number');
        RepositoryBatch::create([
            'number' => $repositoryBatch ? $repositoryBatch->number+1 : 1,
            'quantity' => $request->quantity,
            'price' => $request->price,
            'barcode' => $request->barcode,
            'expired_date' => $request->expired_date,
            'repository_storage_id' => $drugStore->id,
        ]);
        return $this->success();
    }
}
