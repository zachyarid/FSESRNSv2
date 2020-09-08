<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUniqueToAircraft extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fse_groupaircraft', function (Blueprint $table) {
            $table->unique(['group_id', 'serial_number']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fse_groupaircraft', function (Blueprint $table) {
            $table->dropUnique(['group_id', 'serial_number']);
        });
    }
}
