<?php

namespace App\Http\Controllers\User\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\Registration\Pharmacy;
use App\Models\Registration\PharmacyUser;
use App\Models\Registration\Role;
use App\Models\Registration\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Nette\Utils\Random;
use Spatie\Permission\Models\Permission;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('check_permission:employee-pharma');
    }

    public function getPermissions()
    {
        $permissions = Role::with('permissions')
            ->find(Role::ROLE_ADMIN)->permissions;
        return $this->success($permissions);
    }

    public function getRoles(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pharmacy_id' => 'required|exists:pharmacies,id',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());

        $roles = Pharmacy::with(['users' => function ($q) {
            return $q->whereHas('roles', function ($query) {
                $query->where('roles.id', Role::ROLE_ADMIN);
            })->with(['roles' => function ($q) {
                return $q->whereNot('roles.id', Role::ROLE_ADMIN);
            }]);
        }])->find($request->pharmacy_id)->users->first()->roles;
        $roles = $roles->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
            ];
        });
        return $this->success($roles);
    }

    public function createRole(Request $request)
    {
        $user = Auth::user();
        if ($user->roles->first()->id != Role::ROLE_ADMIN) {
            return $this->error("You do not have permeation to this");
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'permissions_ids' => 'required',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());

        $role = Role::create(['name' => $request->name]);
        $permissions = json_decode(json_decode($request->permissions_ids));
        foreach ($permissions as $permission) {
            $permission = Permission::find($permission);
            $role->givePermissionTo($permission);
        }
        $user->assignRole($role->id);
        return $this->success();
    }

    public function getEmployees(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pharmacy_id' => 'required|exists:pharmacies,id',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        return $this->success(Pharmacy::with(['users' => function ($q) {
            return $q->whereHas('roles', function ($q) {
                return $q->whereNot('roles.id', Role::ROLE_ADMIN);
            });
        }])->find($request->pharmacy_id)->users);
    }

    public function getEmployee(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users,id',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        $user = User::where('id', $request->id)->with(['roles' => function ($q) {
            return $q->select('id', 'name');
        }])->first();
        $user = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
            'photo' => $user->photo,
            'role' => $user->roles->first()->name,
        ];
        return $this->success($user);
    }

    public function createEmployee(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input,
            [
                'name' => ['required', 'string', 'max:255'],
                'address' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'phone' => ['required', 'string', 'max:255'],
                'password' => ['required', 'confirmed', Password::defaults()],
                'role_id' => ['required', 'exists:roles,id', Rule::notIn(1, 2)],
                'pharmacy_id' => 'required|exists:pharmacies,id',
            ]);
        if ($validator->fails())
            return $validator->errors()->first();
        try {
            DB::beginTransaction();
            $verify_code = Random::generate(5, '0-9');
//             Mail::to($input['email'])->send(new TestMail($verify_code));
            $user = User::create([
                'name' => $input['name'],
                'address' => $input['address'],
                'phone' => $input['phone'],
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

    public function updateEmployee(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'id' => 'required|exists:users,id',
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'role_id' => ['required', 'exists:roles,id', Rule::notIn(1, 2)],
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());

        $user = User::find($input['id']);
        $user->update([
            'name' => $input['name'],
            'address' => $input['address'],
            'phone' => $input['phone'],
        ]);
        $user->removeRole($user->roles->first());
        $user->assignRole($input['role_id']);
        $user->save();
        return $this->success();
    }

    public function deleteEmployee(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users,id',
            'pharmacy_id' => 'required|exists:pharmacies,id',
        ]);
        if ($validator->fails())
            return $this->error($validator->errors()->first());
        PharmacyUser::where([
            'user_id' => $request->id,
            'pharmacy_id' => $request->pharmacy_id
        ])->first()->delete();
        $parma_user = PharmacyUser::where('user_id', $request->id)->first();
        if (!$parma_user) {
            User::where('id', $request->id)->first()->delete();
        }
        return $this->success();
    }

}
