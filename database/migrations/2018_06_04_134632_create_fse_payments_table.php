<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFsePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fse_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fse_id');
            $table->integer('group_id')->unsigned();
            $table->foreign('group_id')->references('id')->on('groups');
            $table->datetime('date');
            $table->string('p_to')->index();
            $table->string('p_from')->index();
            $table->decimal('amount', 16,2);
            $table->string('reason')->index();
            $table->string('fbo');
            $table->string('location');
            $table->string('aircraft');
            $table->text('comment');
            $table->string('refunded', 3)->nullable();
            $table->datetime('refunded_date')->nullable();
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
        Schema::dropIfExists('fse_payments');
    }
}
