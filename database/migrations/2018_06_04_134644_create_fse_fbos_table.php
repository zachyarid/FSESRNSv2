<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFseFbosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fse_fbos', function (Blueprint $table) {
            $table->integer('id');
            $table->primary('id');
            $table->integer('group_id')->unsigned();
            $table->foreign('group_id')->references('id')->on('groups');
            $table->string('status');
            $table->string('icao')->index();
            $table->string('airport');
            $table->string('name');
            $table->string('owner');
            $table->string('location');
            $table->integer('lots');
            $table->string('repair_shop');
            $table->integer('gates');
            $table->integer('gates_rented');
            $table->integer('fuel_100ll');
            $table->integer('fuel_jeta');
            $table->integer('building_materials');
            $table->integer('supplies');
            $table->integer('supplies_per_day');
            $table->integer('supplied_days');
            $table->decimal('sell_price', 16, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fse_fbos');
    }
}
