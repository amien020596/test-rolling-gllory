<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
            [
                'name' => 'images_number',
                'value' => 5
            ], [
                'name' => 'amount_data_page',
                'value' => 15
            ]
        ]);
    }
}
