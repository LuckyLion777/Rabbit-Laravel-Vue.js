<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['domain' => 'hutch.barntrax.com'], function() {
    Route::get('/{all}', 'HomeController@redirect')->where(['all' => '.*']);
});

Route::post('subscription/stripe/webhook', 'WebhookController@handleWebhook')
    ->middleware('web');

Route::group(['prefix' => 'subscription', 'middleware' => ['web', 'auth'], 'namespace' => 'Subscription'], function() {
    Route::get('plans', 'PlanController@index');
    Route::get('', 'SubscriptionController@show')->name('subscription.show');
    Route::post('preview', 'SubscriptionController@preview')->name('subscriptions.preview');
    Route::post('', 'SubscriptionController@store')->name('subscriptions.create');
    Route::delete('', 'SubscriptionController@destroy')->name('subscriptions.destroy');
    Route::get('invoices', 'InvoiceController@index')->name('subscription.invoices');
    Route::get('invoice/{invoice}', 'InvoiceController@show')->name('subscription.invoice');
});

// Use get so that the user can accept the claimed item using the emailed link
// Besides, auth middleware is not used, so that a guest can get referral id in the session
Route::get('admin/transfer/{transfer}', 'Admin\TransferController@transfer')->middleware(['web'])->name('admin.transfer');
Route::get('admin/users/referrer', 'Admin\AdminUsersController@setReferrerConfirmed')->middleware(['web'])->name('referrer.set');
Route::put('admin/events/list', 'Admin\AdminEventsController@updateList');
Route::get('admin/events/list', 'Admin\AdminEventsController@getList');

Route::group(['prefix' => 'admin', 'middleware' => ['web', 'auth', 'subscribed']], function () {
    Route::post('import', 'Admin\ImportExportController@import');
    Route::post('import/ledgers', 'Admin\ImportExportController@importLedgers');
    Route::get('export', 'Admin\ImportExportController@export');

    Route::post('images/uploadImage', 'ImagesController@uploadImage');

    Route::get('roles/getList', 'Admin\AdminRolesController@getList');
    Route::get('users/referrals', 'Admin\AdminUsersController@referrals');
    Route::get('user/socials', 'Admin\AdminUsersController@socials');
    Route::delete('user/socials/{social}', 'Admin\AdminUsersController@disconnectSocial');
    Route::post('user/web-push-endpoint', 'Admin\AdminUsersController@addWebPushEndpoint');
    Route::post('user/request-test-notification', 'Admin\AdminUsersController@sendTestNotification');
    Route::post('users/referrals', 'Admin\AdminUsersController@addReferral');
    Route::put('users/referrer', 'Admin\AdminUsersController@setReferrer');
    Route::put('users/referrer/dry', 'Admin\AdminUsersController@setReferrerDry');
    Route::get('users/{user}/su', 'Admin\AdminUsersController@su');
    Route::get('users/{users}/settings', 'Admin\AdminUsersController@settings');
    Route::get('users/{users}/settings/getData', 'Admin\AdminUsersController@getSettingsData');
    Route::get('users/{user}/push', 'Admin\AdminUsersController@sendMessage');
    Route::get('users/settings/getDataCurrent', 'Admin\AdminUsersController@getCurrentSettingsData');
    Route::get('users/upcoming', 'Admin\AdminUsersController@upcomingEvents');
    Route::get('users/plans', 'Admin\AdminUsersController@plans');
    Route::post('users/{users}/settings', 'Admin\AdminUsersController@updateSettings');
    Route::get('users/events', 'Admin\AdminUsersController@events');
    Route::get('users/dashboard', 'Admin\AdminUsersController@dashboard');
    Route::post('users/tourShowed', 'Admin\AdminUsersController@tourShowed');
    Route::get('users/getDeathReasons', 'Admin\AdminUsersController@getDeathReasons');

    Route::get('users/getDeathReasonsList', 'Admin\AdminUsersController@getDeathList');
    Route::post('users/saveDeathReasonsList', 'Admin\AdminUsersController@saveDeathReasonsList');

    Route::resource('users', 'Admin\AdminUsersController');

    Route::get('broadcast', 'Admin\BroadcastController@show');
    Route::post('broadcast', 'Admin\BroadcastController@store');
    Route::put('broadcast', 'Admin\BroadcastController@update');
    Route::delete('broadcast', 'Admin\BroadcastController@destroy');

    Route::get('broadcast/active', 'Admin\BroadcastController@active');
    Route::delete('broadcast/active', 'Admin\BroadcastController@dismiss');



    Route::get('plans/{plans}/events', 'Admin\AdminPlansController@events');
    Route::post('plans/{plans}/missed', 'Admin\AdminPlansController@missed');
    Route::get('plans/breeders', 'Admin\AdminPlansController@breeders');
    Route::get('plans/nextLitterId/{plans}', 'Admin\AdminPlansController@nextLitterId');
    Route::resource('plans', 'Admin\AdminPlansController');
    Route::get('fetchBreeders', 'Admin\AdminRabbitBreedersController@fetchBreeders');
    Route::get('fetchBreeder/{id}', 'Admin\AdminRabbitBreedersController@fetchBreeder');
    Route::get('breeders/{id}/html', 'Admin\AdminRabbitBreedersController@getPdfHtml');
    Route::get('breeders/{id}/pdf', 'Admin\AdminRabbitBreedersController@getPdf');
    Route::put('breeders/{breeders}/image', 'Admin\AdminRabbitBreedersController@updateImage');
    Route::get('kits/{kits}/pdf', 'Admin\AdminRabbitKitsController@pdf');
    Route::get('breeders/getList', 'Admin\AdminRabbitBreedersController@getList');
    Route::get('breeders/{breeders}/getLitters', 'Admin\AdminRabbitBreedersController@getLitters');
    Route::get('breeders/{breeders}/getPedigree', 'Admin\AdminRabbitBreedersController@getPedigree');
    Route::get('breeders/{breeders}/events', 'Admin\AdminRabbitBreedersController@events');
    Route::get('breeders/checkId', 'Admin\AdminRabbitBreedersController@checkId');
    Route::post('breeders/{breeders}/archive', 'Admin\AdminRabbitBreedersController@archive');
    Route::post('breeders/{breeders}/sold', 'Admin\AdminRabbitBreedersController@sold');
    Route::post('breeders/{breeders}/butch', 'Admin\AdminRabbitBreedersController@butch');
    Route::post('breeders/{breeders}/died', 'Admin\AdminRabbitBreedersController@died');
    Route::post('breeders/{breeders}/transfer', 'Admin\AdminRabbitBreedersController@transfer');
    Route::get('breeders/checkLimit', 'Admin\AdminRabbitBreedersController@checkLimit');
    Route::get('breeders/checkArchivedLimit', 'Admin\AdminRabbitBreedersController@checkArchivedLimit');
    Route::get('breeders/categories/autocomplete', 'Admin\AdminRabbitBreederCategoriesController@autocomplete');
    Route::post('breeders/{breeders}/butcherValue', 'Admin\AdminRabbitBreedersController@butcherValue');
    Route::post('breeders/{breeders}/deathReasonValue', 'Admin\AdminRabbitBreedersController@deathReasonValue');
    Route::post('breeders/bulk', 'Admin\AdminRabbitBreedersController@saveBreeders');
    Route::resource('breeders/categories', 'Admin\AdminRabbitBreederCategoriesController');
    Route::resource('breeders', 'Admin\AdminRabbitBreedersController');

    Route::get('pedigrees/copy/options', 'Admin\AdminPedigreesController@copyOptions');
    Route::post('pedigrees/copy', 'Admin\AdminPedigreesController@copy');
    Route::resource('pedigrees', 'Admin\AdminPedigreesController');

    Route::get('litters/{litters}/weigh', 'Admin\AdminLittersController@weigh');
    Route::post('litters/{litters}/weigh', 'Admin\AdminLittersController@postWeigh');
    Route::get('litters/{litters}/events', 'Admin\AdminLittersController@events');
    Route::post('litters/{litters}/archive', 'Admin\AdminLittersController@archive');
    Route::get('litters/{litters}/getKits', 'Admin\AdminLittersController@getKits');
    Route::get('litters/{litters}/getDied', 'Admin\AdminLittersController@getDied');
    Route::post('litters/{litters}/butcherValue', 'Admin\AdminLittersController@butcherValue');
    Route::get('litters/getList', 'Admin\AdminLittersController@getList');
    Route::resource('litters', 'Admin\AdminLittersController');

    Route::post('kits/{kits}/weigh', 'Admin\AdminRabbitKitsController@weigh');
    Route::get('kits/{kits}/died', 'Admin\AdminRabbitKitsController@died');
    Route::post('kits/{kits}/revived', 'Admin\AdminRabbitKitsController@revive');
    Route::post('kits/{kits}/archive', 'Admin\AdminRabbitKitsController@archive');
    Route::post('kits/{kits}/sold', 'Admin\AdminRabbitKitsController@sold');
    Route::post('kits/{kits}/transfer', 'Admin\AdminRabbitKitsController@transfer');
    Route::post('kits/butch', 'Admin\AdminRabbitKitsController@butch');
    Route::get('kits/autocomplete', 'Admin\AdminRabbitKitsController@autocomplete');
    Route::get('kits/{kits}/makeBreeder', 'Admin\AdminRabbitKitsController@makeBreeder');
    Route::resource('kits', 'Admin\AdminRabbitKitsController');
    Route::get('kits/{kits}/getPedigree', 'Admin\AdminRabbitKitsController@getPedigree');
    Route::post('kits/{kits}/deathReasonValue', 'Admin\AdminRabbitKitsController@deathReasonValue');

    Route::post('events/makeBreedPlan','Admin\AdminEventsController@makeBreedPlan');
    Route::get('events/test','Admin\AdminEventsController@test');
    Route::get('events/{events}/close','Admin\AdminEventsController@close');
    Route::post('events/archiveEvents', 'Admin\AdminEventsController@archiveEvents');
    Route::get('events/{events}/reopen','Admin\AdminEventsController@reOpen');
    Route::post('events/{events}/unarchive','Admin\AdminEventsController@unarchive');
    Route::get('events/breedPlanDummyEvents','Admin\AdminEventsController@breedPlanDummyEvents');
    Route::resource('events','Admin\AdminEventsController');

    // Premium subscription.
    Route::group(['middleware' => ['subscribed:premium']], function() {
        Route::get('ledger/autocomplete', 'Admin\AdminLedgerController@autocomplete');
        Route::get('ledger/categories/autocomplete', 'Admin\AdminCategoriesController@autocomplete');
        Route::resource('ledger/categories', 'Admin\AdminCategoriesController');
        Route::resource('ledger/entries', 'Admin\AdminLedgerController');
        Route::post('ledger/entries/{entries}/archive', 'Admin\AdminLedgerController@archive');
        Route::get('ledger/statistics', 'Admin\AdminLedgerController@statistics');
        Route::post('ledgers/bulk', 'Admin\AdminLedgerController@saveLedgers');
        Route::get('ledgers/export', 'Admin\ImportExportController@exportLedgers');

        Route::get('reports', 'Admin\AdminReportsController@index');
        Route::get('reports/statistics', 'Admin\AdminReportsController@statistics');
        Route::get('reports/does', 'Admin\AdminReportsController@does');
        Route::get('reports/bucks', 'Admin\AdminReportsController@bucks');


        // Cage cards.
        Route::group(
            [
                'prefix'     => '/cage-cards',
                'as'         => 'cage-cards',
            ],
            function () {
                Route::resource('templates', 'Admin\AdminCageCardsController');

                // Copy template.
                Route::post('/templates/{id}/copy', 'Admin\AdminCageCardsController@copy');

                // Get list of fields for templates dropdown.
                Route::get('/fields-list', 'Admin\AdminCageCardsController@getFieldsList');

                // Get first entities for preview (breeder and litter).
                Route::get('/first-entities', 'Admin\AdminCageCardsController@getFirstEntities');

                // Get user's breeders list.
                Route::get('/breeders', 'Admin\AdminCageCardsController@getBreeders');

                // Get user's litters list.
                Route::get('/litters', 'Admin\AdminCageCardsController@getLitters');

                // Print batch.
                Route::get('/print-batch/{tid}/{eids}', 'Admin\AdminCageCardsController@printBatch');
				// Print batch HTML
                Route::get('/print-batch-html/{tid}/{eids}', 'Admin\AdminCageCardsController@printBatchHTML');
            }
        );
    });

    Route::get('transfers/breeders', 'Admin\TransferController@breeders')->name('admin.transfers.breeders');
    Route::delete('transfer/{transfer}', 'Admin\TransferController@decline');

    Route::get('notifications', 'Admin\NotificationController@index')->name('admin.notifications');
    Route::post('notifications/seen', 'Admin\NotificationController@seen')->name('admin.notifications.seen');
    Route::post('notifications/{notification}/read', 'Admin\NotificationController@read')->name('admin.notification.read');
});

Route::group(['middleware' => 'web'], function () {
    Route::auth();
    Route::post('/auth/device', 'Auth\AuthDeviceController@login');
    Route::get('/auth/{provider}', 'Auth\AuthController@redirectToProvider')->name('auth.forward');
    Route::get('/auth/{provider}/add', 'Auth\AuthController@addProvider')->name('auth.add_provider');
    Route::get('/auth/{provider}/callback', 'Auth\AuthController@handleProviderCallback')->name('auth.callback');
    Route::post('/auth/email', 'Auth\AuthController@setEmail')->name('auth.email');
    Route::get('/', 'HomeController@index');
    Route::get('/colors/List', 'HomeController@index');

    Route::get('pedigree/{id}', 'HomeController@externalRedirect');
    Route::get('p/{id}', 'HomeController@external')->name('web.pedigree');
    Route::get('p/k/{id}', 'HomeController@externalKit')->name('web.kit.pedigree');

    Route::get('clear-cache', 'HomeController@clearCache');

    Route::get('{inviter}', 'HomeController@invite')->where('inviter', '[a-zA-Z0-9]{4}')->name('web.invite');

    Route::get('ics/{user}/{hash}', 'IcsController@download')->name('schedule.export');

    Route::get('wizard/email', 'WizardController@askEmail');
});

/*Route::get('test', function () {
    return response()->json(['error'=>['name']]);
});*/
Route::group(['prefix' => 'api', 'middleware' => 'api'], function () {
    Route::post('register-device', 'Auth\AuthDeviceController@register');
    Route::get('check-device', 'Auth\AuthDeviceController@check');
    Route::get('list-tasks', 'ApiController@listTasks');
    Route::post('update-task', 'ApiController@updateEvent');
});

Route::post('qrcode/upload', ['as'=>'qrcode.upload','uses'=>'HomeController@qrcodeupload']);
Route::match(['get', 'post'], 'qrcode/scan', ['as'=>'qrcode.scan','uses'=>'HomeController@qrcodescan']);