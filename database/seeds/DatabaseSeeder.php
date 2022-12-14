<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(GiftsSeeder::class);
        $this->call(SettingsSeeder::class);
        $this->call(RBACTableSeeder::class);
        $this->call(UsersTableSeeder::class);
    }
}
