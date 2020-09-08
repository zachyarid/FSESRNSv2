<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSerializedMonthlyReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('serialized_monthly_reports', function (Blueprint $table) {
            $table->id();
            $table->integer('subscription_id')->unsigned();
            $table->integer('group_id')->unsigned();
            $table->string('monthyear');
            $table->string('type');
            $table->longtext('content');
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
        Schema::dropIfExists('serialized_monthly_reports');
    }
}
