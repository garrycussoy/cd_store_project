<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRentDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rent_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rent_id')->unsigned();
            $table->integer('cd_id')->unsigned();
            $table->integer('total_items');
            $table->integer('total_price');

            $table->foreign('rent_id')->references('id')->on('rent');
            $table->foreign('cd_id')->references('id')->on('cd');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rent_detail');
    }
}
