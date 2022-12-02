<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;

class RBACTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = ['admin', 'member'];
        $mytime = Carbon\Carbon::now();


        $admin_permissions = ['redem gift', 'make gift', 'update gift'];
        $member_permissions = ['redem gift'];
        foreach ($admin_permissions as $admin_permission) {
            DB::table('permissions')->insertGetId([
                'name' => $admin_permission,
                'guard_name' => 'api',
                'created_at' => $mytime->toDateTimeString(),
                'updated_at' => $mytime->toDateTimeString()
            ]);
        }

        foreach ($roles as $role) {
            $role_id = DB::table('roles')->insertGetId([
                'name' => $role,
                'guard_name' => 'api',
                'created_at' => $mytime->toDateTimeString(),
                'updated_at' => $mytime->toDateTimeString()
            ]);

            if ($role == 'admin') {
                $id_admin_permissions = DB::table('permissions')->select('id')->whereIn('name', $admin_permissions)->get();

                foreach ($id_admin_permissions as $permission_admin_id) {
                    // grant permission to role 
                    DB::table('role_has_permissions')->insert([
                        'permission_id' => $permission_admin_id->id,
                        'role_id' => $role_id
                    ]);
                }
            } else {

                $id_member_permissions = DB::table('permissions')->select('id')->whereIn('name', $member_permissions)->get();
                foreach ($id_member_permissions as $permission_member_id) {
                    // grant permission to role 
                    DB::table('role_has_permissions')->insert([
                        'permission_id' => $permission_member_id->id,
                        'role_id' => $role_id
                    ]);
                }
            }
        }
        // grant permission to role
    }
}
