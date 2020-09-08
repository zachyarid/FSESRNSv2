<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionStatuses extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('subscription_status')->insert([
            [
                'id' => 1,
                'string' => 'Active',
                'status_message' => 'Your subscription is active',
                'error_code' => 200,
                'negative_effect' => 0
            ],
            [
                'id' => 2,
                'string' => 'Inactive',
                'status_message' => 'Your subscription is inactive',
                'error_code' => 401,
                'negative_effect' => 0
            ],
            [
                'id' => 3,
                'string' => 'Cancelled',
                'status_message' => 'Your subscription has been cancelled',
                'error_code' => 404,
                'negative_effect' => 1
            ],
            [
                'id' => 4,
                'string' => 'Past Due',
                'status_message' => 'Your subscription is past due. Please make payment immediately to continue services',
                'error_code' => 402,
                'negative_effect' => 1
            ],
        ]);
    }
}
