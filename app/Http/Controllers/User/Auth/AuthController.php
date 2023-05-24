<?php

namespace App\Http\Controllers\User\Auth;
use App\Http\Controllers\Controller;
use App\Mail\TestMail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Nette\Utils\Random;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'confirmed', Password::defaults()],
                'role_id' => ['required', Rule::in(1,2, 3)],
            ]);
        if ($validator->fails())
            return $validator->errors()->first();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

//        $token = $user->createToken(User::USER_TOKEN);
//        $user->setRememberToken($token->plainTextToken);
//        $user->save();
        return $this->success($user, 'User has been register successfully');
    }

    public function login(Request $request): JsonResponse
    {
        //validation data
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|exists:users',
            'password' => 'required|string|min:6|max:255',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        try {
            DB::beginTransaction();
            //get user by email
            $user = User::where('email', $request->email)->first();
            //check password
            if (Hash::check($request->password, $user->password)) {
                //generate token
                $token = $user->createToken(User::USER_TOKEN);
                $user->setRememberToken($token->plainTextToken);
                $user->save();
                DB::commit();
                return $this->success($user, 'Login successfully');
            } else
                //response failure because password uncorrected
                DB::rollBack();
            return $this->error('password is not matched');
        } catch (\Exception $e) {
            //response failure because Server failure
            return $this->error('Server failure : ' . $e, 500);
        }
    }

    public function loginWithToken(): JsonResponse
    {
        return $this->success(auth()->user(), 'Login successfully');
    }

    public function logout(): JsonResponse
    {
        //expire token
        auth()->user()->currentAccessToken()->delete();
        //response success
        return $this->success(null, 'Logout successfully');
    }

    public function sendVerifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:100|exists:users',
        ]);
        if ($validator->fails())
            return $this->error();
        $user = User::where('email', $request->email)->first();
        $user->verify_code = Random::generate(5, '0-9');
        $user->save();
        try {
            Mail::to($user->email)->send(new TestMail($user->verify_code));
            return $this->success($user);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function verifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:100|exists:users',
            'verifyCode' => 'required|numeric',
        ]);
        if ($validator->fails())
            return $this->error();
        $user = User::where('email', $request->email)->first();
        if ($user->verify_code != $request->verifyCode)
            return $this->error('verifyCode not Correct');
        $user->remember_token = $user->createToken(User::USER_TOKEN)->plainTextToken;
        $user->setRememberToken($user->remember_token);
        $user->email_verified_at = now();
        $user->save();
        return $this->success($user, 'success');
    }

    public function addInfo(Request $request): JsonResponse
    {
        //validation data
        $validator = Validator::make($request->all(), [
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
        } catch (\Exception $e) {
            //response failure
            return $this->error('The photo unusable');
        }
        //response success
        return $this->success();
    }

}
