<?php

namespace App\Http\Controllers\User\Requests;

use App\Events\RequestSentEvent;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Registration\RegistrationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RequestsUserController extends Controller
{
    //        abort_if(Gate::denies('create-request'), Response::HTTP_FORBIDDEN, '403 Forbidden');
//        $this->authorize('create-request');
    public function createRequestRegistration(Request $request)
    {

        $validator = Validator::make($request->all(),
            [
                'name' => ['required', 'string', 'min:7', 'max:50'],
                'address' => ['required', 'string'],
                'type' => ['required', 'string'],
                'status' => ['required', 'string'],
                'phone_number' => ['required', 'string']
            ]);
        if ($validator->fails())
            return $validator->errors()->first();
        try {
            $requestRegister = RegistrationRequest::create([
                'name' => $request->name,
                'type' => $request->type,
                'status' => $request->status,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'owner_id' => auth()->user()->id,
            ]);
            DB::commit();
            $this->sendNotificationToAdmin($requestRegister);
            return $this->success('Request has been sent successfuly');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e);
        }
    }

    /**
     * @param RegistrationRequest $requestRegister
     */
    private function sendNotificationToAdmin(RegistrationRequest $requestRegister){
        event(new RequestSentEvent($requestRegister));

        $user = auth()->user();
        $adminUserId = 1;

        $admin = Admin::where('id',$adminUserId)->first();

        $admin->sendNewRequestNotification([
            'requestData'=>[
                'senderName'=>$user->name,
                'requestType'=>$requestRegister->type,
            ]
        ]);

    }

}
