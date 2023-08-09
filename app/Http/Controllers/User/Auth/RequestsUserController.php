<?php

namespace App\Http\Controllers\User\Auth;

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
                'phone_number' => ['required', 'string'],
                'address' => ['required', 'string'],
                'type' => ['required', 'string'],
                'document_photo' => ['required', 'file'],
            ]);
        if ($validator->fails())
            return $validator->errors()->first();
        $user = auth()->user();
        if (($user->roles->where('id', 1)->first() && $request->type == 'pharmacy') ||
            ($user->roles->where('id', 2)->first() && $request->type == 'repository'))
            try {
                $path = $request->file('document_photo')->storeAs('documents', $request->name . '.' . $request->file('document_photo')->extension(), 'images');
                $path = explode('/', $path);
                RegistrationRequest::create([
                    'name' => $request->name,
                    'phone_number' => $request->phone_number,
                    'address' => $request->address,
                    'type' => $request->type,
                    'document_photo' => $path[1],
                    'user_id' => auth()->user()->id,
                ]);
                DB::commit();
//            $this->sendNotificationToAdmin($requestRegister);
                return $this->success('Request has been sent successfuly');
            } catch (\Exception $e) {
                DB::rollBack();
                return $this->error($e);
            }
        return $this->error('unAuthorized');
    }

    /**
     * @param RegistrationRequest $requestRegister
     */
    private function sendNotificationToAdmin(RegistrationRequest $requestRegister)
    {
        event(new RequestSentEvent($requestRegister));
        $user = auth()->user();
        $adminUserId = 1;

        $admin = Admin::where('id', $adminUserId)->first();
        $user->sendNewRequestNotification([
            'requestData' => [
                'senderName' => $user->name,
                'requestType' => $requestRegister->type,
            ]
        ]);

    }

}
