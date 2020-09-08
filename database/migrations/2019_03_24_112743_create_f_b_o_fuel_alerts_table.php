<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFBOFuelAlertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fbo_fuel_alerts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fbo_id')->unsigned();
            //$table->foreign('fbo_id')->references('id')->on('fse_fbos');
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
        Schema::dropIfExists('fbo_fuel_alerts');
    }
}
