<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserStatuses extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_status')->insert([
            ['id' => 1, 'string' => 'Active', 'status_message' => 'Your account is active and in good standing', 'error_code' => 200, 'negative_effect' => 0],
            ['id' => 2, 'string' => 'Inctive', 'status_message' => 'Your account is inactive but in good standing', 'error_code' => 401, 'negative_effect' => 0],
            ['id' => 3, 'string' => 'Banned', 'status_message' => 'Your account has been banned', 'error_code' => 418, 'negative_effect' => 1]
        ]);
    }
}
