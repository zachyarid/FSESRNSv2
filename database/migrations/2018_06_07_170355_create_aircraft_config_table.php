<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAircraftConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aircraft_config', function (Blueprint $table) {
            $table->string('make_model');
            $table->primary('make_model');
            $table->integer('crew');
            $table->integer('seats');
            $table->integer('cruise_speed');
            $table->integer('gph');
            $table->integer('fuel_type');
            $table->integer('mtow');
            $table->integer('empty_weight');
            $table->decimal('price', 10,2);
            $table->integer('ext1');
            $table->integer('ltip');
            $table->integer('laux');
            $table->integer('lmain');
            $table->integer('center1');
            $table->integer('center2');
            $table->integer('center3');
            $table->integer('rmain');
            $table->integer('raux');
            $table->integer('rtip');
            $table->integer('ext2');
            $table->integer('engines');
            $table->decimal('engine_price', 10,2);
            $table->integer('model_id');
            $table->string('alias');
            $table->integer('enabled');
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
        Schema::dropIfExists('aircraft_config');
    }
}
