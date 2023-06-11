<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\AppDetails;
use App\Models\RoleHasApplication;
use Hash;
use DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $applicationList = AppDetails::all();

        foreach($applicationList as $obj){
        
            $checkExist = RoleHasApplication::where(['role_id'=> 1 , 'application_id' => $obj->id])->exists();

            if(!$checkExist){
                RoleHasApplication::create(
                    [
                        'role_id' => 1,
                        'account_id' => $obj->account_id,
                        'application_id' => $obj->id,
                    ]
                );                    
                
            }            
        }        



        // $checkPermissionExist = Permission::where('name', 'view-manage-push_notifications')->exists();

        // if (!$checkPermissionExist) {

        //     $permission = Permission::create(
        //         [
        //             'name' => 'view-manage-push_notifications'
        //         ]
        //     );

        //     DB::table('role_has_permissions')->insert(['permission_id' => $permission['id'], 'role_id' => 1]);

        // }

/*

        $checkPermissionExist = Permission::where('name', 'view-manage-sync_accounts_data')->exists();

        if (!$checkPermissionExist) {

            $permission = Permission::create(
                [
                    'name' => 'view-manage-sync_accounts_data'
                ]
            );

            DB::table('role_has_permissions')->insert(['permission_id' => $permission['id'], 'role_id' => 1]);

        }

        $checkPermissionExist = Permission::where('name', 'view-manage-sync_apps_data')->exists();

        if (!$checkPermissionExist) {

            $permission = Permission::create(
                [
                    'name' => 'view-manage-sync_apps_data'
                ]
            );

            DB::table('role_has_permissions')->insert(['permission_id' => $permission['id'], 'role_id' => 1]);

        }

        $checkPermissionExist = Permission::where('name', 'view-push_notifications')->exists();

        if (!$checkPermissionExist) {

            $permission = Permission::create(
                [
                    'name' => 'view-push_notifications'
                ]
            );

            DB::table('role_has_permissions')->insert(['permission_id' => $permission['id'], 'role_id' => 1]);

        }


        $checkPermissionExist = Permission::where('name', 'manage-push_notifications')->exists();

        if (!$checkPermissionExist) {

            $permission = Permission::create(
                [
                    'name' => 'manage-push_notifications'
                ]
            );

            DB::table('role_has_permissions')->insert(['permission_id' => $permission['id'], 'role_id' => 1]);

        }


        $checkPermissionExist = Permission::where('name', 'view-server-types')->exists();

        if (!$checkPermissionExist) {

            $permission = Permission::create(
                [
                    'name' => 'view-server-types'
                ]
            );

            DB::table('role_has_permissions')->insert(['permission_id' => $permission['id'], 'role_id' => 1]);

            $permission = Permission::create(
                [
                    'name' => 'manage-server-types'
                ]
            );

            DB::table('role_has_permissions')->insert(['permission_id' => $permission['id'], 'role_id' => 1]);

        }

        $checkPermissionExist = Permission::where('name', 'view-block-app-countries')->exists();

        if (!$checkPermissionExist) {

            $permission = Permission::create(
                [
                    'name' => 'view-block-app-countries'
                ]
            );

            DB::table('role_has_permissions')->insert(['permission_id' => $permission['id'], 'role_id' => 1]);

        }


        $checkPermissionExist = Permission::where('name', 'manage-block-app-countries')->exists();

        if (!$checkPermissionExist) {

            $permission = Permission::create(
                [
                    'name' => 'manage-block-app-countries'
                ]
            );

            DB::table('role_has_permissions')->insert(['permission_id' => $permission['id'], 'role_id' => 1]);

        }
*/
//        $role = Role::create(['name' => 'super-admin']);
//
//        $permissions = Permission::pluck('id','id')->all();
//
//        $role->syncPermissions($permissions);
//
//        $user->assignRole([$role->id]);

    }
}
