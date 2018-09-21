<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('order-item');
        Schema::create('order-item', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('order-id');
            $table->integer('product-id');
            $table->integer('quatity');
            $table->double('unit-price', 8, 2);
            $table->double('total', 8, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {}
}