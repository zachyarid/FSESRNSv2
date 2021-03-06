<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSubscriptionStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscription_status', function (Blueprint $table) {
            $table->string('status_message')->after('string');
            $table->integer('error_code')->after('status_message');
            $table->integer('negative_effect')->default(0)->after('error_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscription_status', function (Blueprint $table) {
            $table->dropColumn([
                'status_message',
                'error_code',
                'negative_effect'
            ]);
        });
    }
}
