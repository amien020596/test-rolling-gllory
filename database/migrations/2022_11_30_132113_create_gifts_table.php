<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gifts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->mediumText('name');
            $table->longText('description');
            $table->float('rating', 8, 2)->default(0);
            $table->json('images');
            $table->bigInteger('price')->default(0);
            $table->boolean('new_gift');
            $table->integer('quantity')->default(0);
            $table->bigInteger('wishlist')->default(0);
            $table->bigInteger('reviews')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gifts');
    }
}
