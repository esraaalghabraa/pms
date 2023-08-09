<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\Registration\Role;
use App\Models\Registration\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Nette\Utils\Random;
use Illuminate\Support\Facades\Auth;

class AuthUserController extends Controller
{
    public function register(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input,
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'confirmed', Password::defaults()],
                'role_id' => ['required', Rule::in(Role::ROLE_ADMIN, Role::ROLE_Supplier)],
            ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        try {
            DB::beginTransaction();
            $verify_code = Random::generate(5, '0-9');
            //Mail::to($input['email'])->send(new TestMail($verify_code));
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
                'verify_code' => $verify_code,
            ]);
            $user->assignRole($request->role_id);
            DB::commit();
            return $this->success('send verify code successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e);
        }
    }

    public function login(Request $request)
    {
        $input = $request->all();
        $validation = Validator::make($input, [
            'email' => 'required|string|email|exists:users',
            'password' => 'required|string|min:6|max:255',
        ]);
        if ($validation->fails())
            return $this->error($validation->errors()->first());
        try {
            DB::beginTransaction();
            if (Auth::guard('user')->attempt(['email' => $input['email'], 'password' => $input['password']])) {
                $user = Auth::guard('user')->user();
                $token = $user->remember_token = $user->createToken(User::USER_TOKEN, ['user'])->plainTextToken;
                $user->setRememberToken($token);
                $user->verify_code = null;
                $user->save();
                $result = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'address' => $user->address,
                    'photo' => $user->photo,
                    'token' => $token,
                    'roles' => $user->roles->map(function ($role) {
                        return [
                            'id' => $role->id,
                            'name' => $role->name,
                            'permissions' => $role->permissions,
                        ];
                    }),
                ];
                DB::commit();
                return $this->success($result, 'success');
            } else {
                return $this->error('Your information is incorrect');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e);
        }
    }

    public function verifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:100|exists:users',
            'verifyCode' => 'required|numeric',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        try {
            DB::beginTransaction();
            $user = User::where('email', $request->email)->first();
            if ($user->verify_code != $request->verifyCode)
                return $this->error('verifyCode not Correct');
            $token = $user->remember_token = $user->createToken(User::USER_TOKEN, ['user'])->plainTextToken;
            $user->setRememberToken($token);
            $user->verify_code = null;
            $user->save();
            DB::commit();
            return $this->success($token, 'success');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Server failure : ' . $e, 500);
        }
    }

    public function sendVerifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:100|exists:users',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        try {
            $user = User::where('email', $request->email)->first();
            $verify_code = Random::generate(5, '0-9');
            //Mail::to($request->email)->send(new TestMail($verify_code));
            $user->verify_code = $verify_code;
            $user->save();
            return $this->success('send verify code successfully');
        } catch (\Exception $e) {
            return $this->error('Server failure : ' . $e, 500);
        }
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();
        return $this->success();
        //TODO delete account
//        auth()->user()->delete();
//        return $this->success(null, 'delete account successfully');
    }

    public function addInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users',
            'photo' => 'mimes:jpg,jpeg,png,jfif',
            'name' => 'string|max:255',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        //get user by token
        $user = User::find($request->user()->id);
        try {
            //store photo in project and database
            if ($request->has('photo')) {
                $name = explode(' ', $user->name);
                $path = $request->file('photo')->storeAs('users', $name[0] . '.' . $request->file('photo')->extension(), 'images');
                $path = explode('/', $path);
                $user->update(['photo' => $path[1]]);
                $user->save();
            }
            if ($request->has('name')) {
                $user->update(['name' => $request->name]);
                $user->save();
            }
            if ($request->has('phone')) {
                $user->update(['phone' => $request->phone]);
                $user->save();
            }
            if ($request->has('address')) {
                $user->update(['address' => $request->address]);
                $user->save();
            }
        } catch (\Exception $e) {
            //response failure
            return $this->error('The photo unusable');
        }
        //response success
        return $this->success();
    }

    public function getInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        //get user by token
        $user = User::find($request->user()->id);
        return $this->success($user);
    }
}
