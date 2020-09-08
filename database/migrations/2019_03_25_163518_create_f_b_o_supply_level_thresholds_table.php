<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFBOSupplyLevelThresholdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fbo_supply_level_thresholds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('subscription_id')->unsigned();
            //$table->foreign('subscription_id')->references('id')->on('subscriptions');
            $table->integer('supply_threshold')->default(3000);
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
        Schema::dropIfExists('fbo_supply_level_thresholds');
    }
}
