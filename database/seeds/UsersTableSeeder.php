<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $mytime = Carbon\Carbon::now();

        $id = DB::table('users')->insertGetId([
            'name' => 'administrator',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'created_at' => $mytime->toDateTimeString(),
            'updated_at' => $mytime->toDateTimeString()
        ]);

        $user = User::find($id);
        $user->assignRole(Role::findByName('admin', 'api'));
        $user->save();
    }
}
