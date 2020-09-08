<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAutoResupplyParamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auto_resupply_params', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('subscription_id')->unsigned();
            //$table->foreign('subscription_id')->references('id')->on('subscription');
            $table->integer('resupply_days');
            $table->integer('resupply_amount');
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
        Schema::dropIfExists('auto_resupply_params');
    }
}
