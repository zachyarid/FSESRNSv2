<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFseGroupaircraftTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fse_groupaircraft', function (Blueprint $table) {
            $table->integer('group_id')->unsigned();
            $table->foreign('group_id')->references('id')->on('groups');
            $table->integer('serial_number');
            $table->primary(['serial_number', 'group_id']);
            $table->string('make_model');
            $table->string('registration');
            $table->string('owner');
            $table->string('location');
            $table->string('location_name');
            $table->string('home');
            $table->decimal('sale_price', 10,2);
            $table->decimal('sell_back_price',10,2);
            $table->string('equipment');
            $table->decimal('rental_dry',8,2);
            $table->decimal('rental_wet',8,2);
            $table->integer('bonus');
            $table->integer('rental_time');
            $table->string('rented_by');
            $table->decimal('fuel_pct', 3,2);
            $table->integer('needs_repair');
            $table->string('airframe_time');
            $table->string('engine_time');
            $table->string('time_last_100hr');
            $table->decimal('monthly_fee',8,2);
            $table->decimal('fee_owed',8,2);
            $table->string('leased_from');
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
        Schema::dropIfExists('fse_groupaircraft');
    }
}
