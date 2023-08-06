<?php

namespace App\Http\Controllers\User\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\Registration\Role;
use App\Models\Registration\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Traits\HasRoles;

class RoleController extends Controller
{
    // TODO ESRAA
    public function getAll()
    {
        $roles = Role::with('permissions')->get();
//        $data['roles']=$roles;
//        $data['permissions']=$permissions;
        return $this->success($roles);
    }

    public function create(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'role' => ['required', 'string','unique:roles,name'],
            'permissions' => ['required'],
        ]);
        if ($validator->fails())
            return $validator->errors()->first();
        $role = Role::create([
            'name'=>$input['role']
        ]);
        $permissions= json_decode(json_decode($input['permissions']));
        foreach ($permissions as $permission)   {
            $employee_permission = Permission::where('name',$permission->name)->get()->first();
            if (!$employee_permission)
                $employee_permission= Permission::create([
                    'name' => $permission->name,
                    'guard_name' => 'user'
                ]);
            $role->givePermissionTo($employee_permission);
        }
        return response()->json($role, 201);
    }

    public function update(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'id'=>['required','exists:roles,id'],
            'name' => ['required', 'string','unique:roles,name,' . $request->id],
            'permissions' => ['required'],
        ]);
        if ($validator->fails())
            return $validator->errors()->first();
        $role = Role::with('permissions')->findOrFail($request->id);
        $role->update([
            'name'=>$input['name']
        ]);
        $role->save();
        $permissions= json_decode(json_decode($input['permissions']));
        foreach ($permissions as $permission)   {
            $employee_permission= Role::with(['permissions'=>function($q) use($permission){
                return $q->where('permissions.id',$permission->id);
            }])->findOrFail($role->id)->permissions;
            if (!$employee_permission) {
                $employee_permission = Permission::firstOrCreate([
                    'name' => $permission->name,
                    'guard_name' => 'user',
                ]);

                // Assign the permission to the role
                $role->givePermissionTo($employee_permission);
            } else {
                // Update the permission's name if it already exists
                foreach ($employee_permission as $item)
                    $item->name = $permission->name;
                    $item->save();
            }
            $role->save();
        }
        return $this->success();
    }

    public function destroy(Request $request)
    {
        $users = User::with(['roles'=>function($q) use($request){
          return $q->where('model_has_roles.role_id',$request->id);
      }])->first();

      return $this->success($users->roles);
    }
}
