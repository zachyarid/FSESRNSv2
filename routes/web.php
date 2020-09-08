<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();


//Route::prefix('apiv2')->group(function () {
    Route::get('/erbvd3/all-in', function() {
        $one = \App\FSEAssignment::where('consumed', 0)->where('type', 'All-In')->orderBy('pay', 'desc')->firstOrFail();
        $one->consumed = 1;
        $one->save();

        return $one;
    });
//});

Route::middleware(['auth'])->group(function () {
    Route::middleware([\App\Http\Middleware\CheckAccountStatus::class])->group(function () {
        Route::get('/', 'HomeController@index')->name('home');

        Route::get('/profile', 'HomeController@profile')->name('profile');
        Route::post('/profile', 'HomeController@updateProfile');

        Route::post('/date', 'HomeController@setReportDate')->name('home.date');

        Route::get('/test/{subscription}', 'ReportController@test');

        Route::prefix('subscriptions')->group(function () {
            Route::get('', 'SubscriptionController@index')->name('subscriptions.index');
            Route::get('list', 'SubscriptionController@list')->name('subscriptions.list');
            Route::get('add/{service}', 'SubscriptionController@create')->name('subscriptions.create');
            Route::post('add', 'SubscriptionController@store')->name('subscriptions.store');
            Route::get('cancel/{subscription}', 'SubscriptionController@cancel')->name('subscriptions.cancel');
            Route::delete('{subscription}', 'SubscriptionController@doCancel')->name('subscriptions.doCancel');
        });


        Route::prefix('useraccess')->group(function () {
            Route::get('', 'UserAccessController@index')->name('useraccess.index');
            Route::get('add', 'UserAccessController@create')->name('useraccess.create');
            Route::post('add', 'UserAccessController@store')->name('useraccess.store');
            Route::delete('{sid}/{uid}', 'UserAccessController@destroy')->name('useraccess.destroy');
        });

        Route::prefix('payments')->group(function () {
            Route::get('', 'PaymentController@index')->name('payments.index');
            Route::get('add', 'PaymentController@create')->name('payments.create');
            Route::post('add', 'PaymentController@store')->name('payments.store');
        });

        Route::middleware([\App\Http\Middleware\CheckSubscriptionStatus::class])->group(function () {
            Route::prefix('report')->group(function () {
                Route::get('{subscription}', 'ReportController@view')->name('report.view');
                Route::get('{subscription}/fbopandl/{fboid}/{num}', 'ReportController@pAndL')->name('report.pandl');#
                Route::get('{subscription}/aircraft/{aircraft}', 'ReportController@viewAircraft')->name('report.aircraft');
                Route::get('{subscription}/pilot/{pilot}', 'ReportController@viewPilot')->name('report.pilot');
                Route::get('{subscription}/fbopandl', 'ReportController@requestFBOPandL')
                    ->middleware([\App\Http\Middleware\LimitMassFBOPandLRequests::class])->name('report.requestpandl');
            });

            Route::prefix('monitor')->group(function () {
                Route::get('{subscription}', 'MonitorController@view')->name('monitor.view');
                Route::post('{subscription}/auto', 'MonitorController@saveAutoResupplyFBOs')->name('monitor.saveauto');
            });
        });

        Route::post('/monitor/ft', 'MonitorController@changeFuelThresholds')->name('monitor.changeft');
        Route::post('/monitor/st', 'MonitorController@changeSupplyThresholds')->name('monitor.changest');
        Route::post('/monitor/arp', 'MonitorController@changeAutoResupplyParams')->name('monitor.changearp');



        // check admin middleware / policy
        Route::middleware([\App\Http\Middleware\IsAdmin::class])->group(function() {
            Route::prefix('admin')->group(function() {
                Route::get('', 'AdminController@index');

                Route::prefix('subscriptions')->group(function () {
                    Route::get('', 'AdminController@subscriptionsIndex')->name('admin.subscriptions');
                    Route::get('add', 'AdminController@subscriptionsCreate')->name('admin.subscription.add');
                    Route::post('add', 'AdminController@subscriptionsStore');
                    Route::get('manage/{subscription}', 'AdminController@subscriptionsManage')->name('admin.subscriptions.manage');
                    Route::get('cancel/{subscription}', 'AdminController@subscriptionsCancel')->name('admin.subscriptions.cancel');
                    Route::get('reactivate/{subscription}', 'AdminController@subscriptionsReactivate')->name('admin.subscriptions.reactivate');
                });

                Route::prefix('groups')->group(function () {
                    Route::get('', 'AdminController@groupsIndex')->name('admin.groups');
                    Route::get('add', 'AdminController@groupsStore')->name('admin.group.add');
                    Route::get('discron/{group}', 'AdminController@groupsDisableCron')->name('admin.group.discron');
                });

                Route::prefix('payments')->group(function () {
                    Route::get('', 'AdminController@paymentsIndex')->name('admin.payments');
                    Route::get('confirm/{payment}', 'AdminController@paymentsConfirm')->name('admin.payment.confirm');
                });

                Route::prefix('users')->group(function () {
                    Route::get('', 'AdminController@usersIndex')->name('admin.users');
                    Route::get('add', 'AdminController@usersStore')->name('admin.users.add');
                    Route::post('add', 'AdminController@userStore');
                    Route::get('view/{user}', 'AdminController@usersView')->name('admin.users.view');
                    Route::get('active/{user}', 'AdminController@usersActive')->name('admin.users.active');
                    Route::get('inactive/{user}', 'AdminController@usersInactive')->name('admin.users.inactive');
                    Route::get('ban/{user}', 'AdminController@usersBan')->name('admin.users.ban');
                });
            });
        });

        Route::resource('groups', 'GroupsController');
        Route::resource('special-payments', 'SpecialPaymentController');
        Route::get('/logout', 'HomeController@logout');
    });
});
