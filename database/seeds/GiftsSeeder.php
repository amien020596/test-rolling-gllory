<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class GiftsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $faker = Faker::create();
        for ($i = 1; $i <= 50; $i++) {
            DB::table('gifts')->insert([
                'description' => $faker->text(),
                'images' => json_encode([
                    'https://picsum.photos/seed/picsum/200/300',
                    'https://picsum.photos/seed/picsum/200/300'
                ]),
                'new_gift' => $faker->boolean(),
                'price' => $faker->numberBetween(2000000, 5000000),
                'wishlist' => $faker->numberBetween(0, 100),
                'reviews' => $faker->numberBetween(0, 100),
                'quantity' => $faker->numberBetween(0, 5),
                'rating' => $faker->randomFloat(2, 2, 5),
                'name' => "Samsung S Series " . $faker->randomFloat(2, 1, 5),
            ]);
        }
    }
}
