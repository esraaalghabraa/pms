<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Mail\TestMail;
use App\Models\Registration\FrontUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Nette\Utils\Random;

class AuthUserController extends Controller
{
    public function register(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input,
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:front_users'],
                'password' => ['required', 'confirmed', Password::defaults()],
                'role_id' => ['required', Rule::in(1, 2, 3)],
            ]);
        if ($validator->fails())
            return $validator->errors()->first();
        try {
            DB::beginTransaction();
            $verify_code = Random::generate(5, '0-9');
            Mail::to($input['email'])->send(new TestMail($verify_code));
            $user = FrontUser::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
                'role_id' => $input['role_id'],
                'verify_code' => $verify_code,
            ]);
            DB::commit();
            return $this->success('send verify code successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error();
        }
    }

    public function login(Request $request)
    {
        $input = $request->all();
        $validation = Validator::make($input, [
            'email' => 'required|string|email|exists:front_users',
            'password' => 'required|string|min:6|max:255',
        ]);
        if ($validation->fails())
            return $this->error($validation->errors()->first());
        try {
            DB::beginTransaction();
            if (Auth::guard('frontuser')->attempt(['email' => $input['email'], 'password' => $input['password']])) {
                $user = Auth::guard('frontuser')->user();
                $verify_code = Random::generate(5, '0-9');
                Mail::to($user->email)->send(new TestMail($verify_code));
                DB::commit();
                return $this->success('send verify code successfully');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error();
        }
        return $this->error();
    }

    public function verifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:100|exists:front_users',
            'verifyCode' => 'required|numeric',
        ]);
        if ($validator->fails())
            return $this->error();
        try {
            DB::beginTransaction();
            $user = FrontUser::where('email', $request->email)->first();
            if ($user->verify_code != $request->verifyCode)
                return $this->error('verifyCode not Correct');
            $token = $user->remember_token = $user->createToken('myApp', ['frontuser'])->plainTextToken;
            $user->setRememberToken($token);
            $user->save();
            DB::commit();
            return $this->success($token, 'success');
        } catch (\Exception $e) {
            //response failure because Server failure
            return $this->error('Server failure : ' . $e, 500);
        }
    }

    public function logout(): JsonResponse
    {
        $user = Auth::user();
        return response()->json(["data"=>$user]);
//        auth()->user()->currentAccessToken()->delete();
//        return $this->success(null, 'Logout successfully');
    }

}
