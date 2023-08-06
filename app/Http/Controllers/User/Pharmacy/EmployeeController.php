<?php

namespace App\Http\Controllers\User\Pharmacy;

use App\Http\Controllers\Controller;
use App\Mail\TestMail;
use App\Models\Registration\Pharmacy;
use App\Models\Registration\PharmacyUser;
use App\Models\Registration\Role;
use App\Models\Registration\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Nette\Utils\Random;
use Spatie\Permission\Models\Permission;

class EmployeeController extends Controller
{
    // TODO ESRAA
    public function __construct()
    {
        $this->middleware('check_permission:employee-pharma');
    }

    public function getAll(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pharmacy_id' => 'required|exists:pharmacies,id',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        return $this->success(Pharmacy::with(['users'=>function($q){
            return $q->whereHas('roles',function ($query){
                $query->whereNot('roles.id',Role::ROLE_ADMIN);
            });
        }])->find($request->pharmacy_id));
    }

    public function get(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users,id',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        return $this->success(User::where('id', $request->id)->first());
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users,id',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        PharmacyUser::where('user_id', $request->id)->first()->delete();
        User::where('id', $request->id)->first()->delete();
        return $this->success();
    }

    public function create(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input,
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'confirmed', Password::defaults()],
                'role_id' => ['required', 'exists:roles,id', Rule::notIn(1, 2)],
                'pharmacy_id' => 'required|exists:pharmacies,id',
            ]);
        if ($validator->fails())
            return $validator->errors()->first();
        try {
            DB::beginTransaction();
            $verify_code = Random::generate(5, '0-9');
//            Mail::to($input['email'])->send(new TestMail($verify_code));
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
                'verify_code' => $verify_code,
            ]);
            $user->assignRole($input['role_id']);
            PharmacyUser::create([
                'user_id' => $user->id,
                'pharmacy_id' => $input['pharmacy_id'],
            ]);
            DB::commit();
            return $this->success('send verify code successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e);
        }
    }
}
