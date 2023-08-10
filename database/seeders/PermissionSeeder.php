<?php

namespace Database\Seeders;


use App\Models\Registration\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $adminRole = Role::where('id', Role::ROLE_PHARMACY)->first();
        $supplierRole = Role::where('id', Role::ROLE_REPOSITORY)->first();

        $pharma_permissions = [
            'stored-medicines',
            'orders-buy-medicines',
            'sales-customers',
            'employees',
        ];
        foreach ($pharma_permissions as $permission) {
            $pharma_permission = Permission::create([
                'name' => $permission,
                'guard_name' => 'user'
            ]);
            $adminRole->givePermissionTo($pharma_permission);
        }

        $repo_permissions = [
            'all',
        ];

        foreach ($repo_permissions as $permission) {
            $repo_permission = Permission::create([
                'name' => $permission,
                'guard_name' => 'user'
            ]);
            $supplierRole->givePermissionTo($repo_permission);
        }
    }
}
