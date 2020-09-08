<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFseAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fse_assignments', function (Blueprint $table) {
            $table->integer('id');
            $table->primary('id');
            $table->string('location');
            $table->string('to_icao');
            $table->string('from_icao');
            $table->string('amount');
            $table->string('unit_type');
            $table->string('commodity');
            $table->decimal('pay', 8,2);
            $table->string('expires');
            $table->timestamp('expires_timestamp');
            $table->string('type');
            $table->boolean('express');
            $table->string('pt_assignment');
            $table->integer('aircraft_id');
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
        Schema::dropIfExists('fse_assignments');
    }
}
