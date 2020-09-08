<?php

namespace App\Providers;

use App\Mail\JobFailedEmail;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
       Schema::defaultStringLength(191);

        Route::resourceVerbs([
            'create' => 'add',
        ]);

        Queue::failing(function (JobFailed $event) {
            Mail::to('zach.yarid@gmail.com')->send(new JobFailedEmail($event));
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(TelescopeServiceProvider::class);
    }
}
