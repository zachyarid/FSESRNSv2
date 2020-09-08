<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFseFlightlogGroupaircraftTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fse_flightlog_groupaircraft', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fse_id');
            $table->integer('group_id')->unsigned();
            $table->foreign('group_id')->references('id')->on('groups');
            $table->string('type');
            $table->datetime('date');
            $table->integer('distance');
            $table->string('pilot');
            $table->integer('serial_number');
            $table->string('aircraft');
            $table->string('make_model');
            $table->string('f_from');
            $table->string('f_to');
            $table->string('total_engine_time');
            $table->string('flight_time');
            $table->string('group_name');
            $table->decimal('income',10,2);
            $table->decimal('pilot_fee', 10,2);
            $table->decimal('crew_cost',10,2);
            $table->decimal('booking_fee',10,2);
            $table->decimal('bonus',10,2);
            $table->decimal('fuel_cost', 10,2);
            $table->decimal('gcf', 10,2);
            $table->decimal('rental_price',10,2);
            $table->string('rental_units');
            $table->decimal('rental_cost',10,2);

            $table->unique(['fse_id', 'group_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fse_flightlog_groupaircraft');
    }
}
