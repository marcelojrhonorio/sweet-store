<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
| ...
 */
if(config('app.env') === 'production') {
    \URL::forceScheme('https');
}

Route::get('/slack', function () {
 
    $user = App\Models\Customer::first();
     
    $user->notify(new App\Notifications\ExchangePoints());
     
    echo "A slack notification has been send";
     
});

Route::get('/ssiPostback', function () {
 
    $ssiPostbackNotification = new App\Models\Notifications\SsiPostbackNotification();
     
    $ssiPostbackNotification->notify(new App\Notifications\SsiPostback());
     
    echo "A slack notification has been send";
     
});

Route::get('/click-login', 'ClickLoginController@login');

Route::get('/click-checkin', 'ClickCheckinController@handle');
    
Route::get('/login/verify/{token}', 'LoginController@verify');
    
Route::get('/pixelemail', 'DoubleOptinPixelController@handle');

Route::get('/app-messages/{messageId}', 'MobileApp\AppMessagePostbackController@getMessage');
    
Route::get('/login/verify/', function () {
    return redirect('/');
});

Route::group(['prefix' => 'share-action'], function () {
    Route::get('/', 'MemberGetMemberController@index');
    Route::get('/postback', 'ShareActionController@postback');
    Route::get('/login', 'HomeController@fromShare');
    Route::get('/redirect', 'ShareActionController@postbackShare');
});

Route::group(['prefix' => '/unsubscribe'], function () {
    Route::get('/', 'UnsubscribeController@index');
    Route::post('/', 'UnsubscribeController@unsubscribe');
});

Route::post('/vip-list/create', 'VipListSubscribersController@create');

Route::post('/vip-list/verify', 'VipListSubscribersController@verify');

Route::post('/vip-list/update', 'VipListSubscribersController@update');
   
Route::get('/researches/postback', 'ResearchesController@postback');

Route::get('/survey/ssi/{email}', 'SsiController@showSurvey');
    
Route::get('/survey/ssi/postback/{postback}', 'SsiController@postback');
    
Route::get('/researches/resume', 'ResearchesController@resume');
    
Route::post('/researches/insurance', 'ResearchesInsuranceController@store');
Route::post('/researches/insurance/step-1', 'ResearchesInsuranceController@stepOne');
Route::post('/researches/insurance/step-2', 'ResearchesInsuranceController@stepTwo');
Route::post('/researches/insurance/step-3', 'ResearchesInsuranceController@stepThree');
Route::get('/researches/insurance/lead-dispatch', 'ResearchesInsuranceController@leadDispatch');
    
Route::get('/featured', 'FeaturedActionsController@index');
    
Route::group(['prefix' => 'incentive-emails'], function () {
    Route::get('/postback', 'IncentiveEmails\CheckinIncentiveEmailsController@postback');
});
    
Route::middleware(['isAdministrator'])->group(function () {
    Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
});
    
Route::middleware(['isGuest'])->group(function () {
    Route::get('/login', 'LoginController@index');

    Route::post('/login', 'LoginController@login');

    Route::get('/loginFromShare', 'LoginController@loginShare');

    Route::get('/login/verify-email', 'LoginController@verifyEmail');

    Route::get('/password/receive', 'Password\ForgotPasswordController@showReceivePasswordForm');

    Route::post('/password/receive', 'Password\ForgotPasswordController@sendNewPasswordEmail');

    Route::get('/password/change', 'Password\ChangePasswordController@showForm');

    Route::post('/password/change', 'Password\ChangePasswordController@change');

    Route::get('/password/create', 'Password\CreatePasswordController@showForm');

    Route::post('/password/create', 'Password\CreatePasswordController@create');

});
    
Route::middleware(['isLogged'])->group(function () {
    Route::get('/', 'HomeController@index');
    
    Route::get('/logout', 'LoginController@logout');

    Route::group(['prefix' => 'my-account'], function () {
        Route::get('/', 'Account\AccountController@index');
    });

    Route::group(['prefix' => 'email-forwarding'], function () {
        Route::post('/', 'EmailForwardingController@store');
        Route::post('/submit-proof/upload', 'EmailForwardingController@uploadImage');
        Route::post('/send-email', 'EmailForwardingController@sendEmail');
    });

    Route::post('/receive-offers/update', 'ProfileController@updateReceiveOffers');

    Route::post('/customer-login-points/create', 'LoginController@createLoginPoints');
    
    Route::put('/profile/email', 'ProfileEmailController@update');
    
    Route::post('/profile/email/resend', 'ProfileEmailController@resend');
    
    Route::get('/profile', 'ProfileController@index');

    Route::post('/profile/update', 'ProfileController@addressRegister');
    
    Route::post('/profile/interest/create', 'ProfileController@createCustomersInterest');

    Route::post('/profile/interest/delete', 'ProfileController@deleteCustomersInterest');

    Route::put('/profile/upload', 'ProfileController@upload');
    
    Route::get('/earn', 'EarnPointsController@index');
    
    Route::get('/earn/{categoryId}', 'EarnPointsController@listByCategory');
    
    Route::get('/researches','ResearchesSsiStoreController@index');
    
    Route::get('/stamps', 'Stamps\StampsController@index');
    
    Route::group(['prefix' => 'exchange'], function () {
        Route::get('/', 'Exchange\ExchangePointsController@index');
        Route::get('/get-last-address/{customerId}', 'Exchange\CheckoutController@getLastAddress');
        Route::get('{categoryId}', 'Exchange\ListItemsController@listByCategory');
        Route::get('min/{priceMin}/max/{priceMax}', 'Exchange\ListItemsController@listByPriceRange');
        //Route::get('{categoryId}', 'Exchange\CheckoutController@getProductServiceStamps');

        Route::post('checkout', 'Exchange\CheckoutController@checkout');
        Route::post('/get-product-service', 'Exchange\CheckoutController@getProductService');
        Route::post('/verify-stamps-required', 'Exchange\CheckoutController@verifyStampsRequired');
        Route::post('/get-customer-points', 'Exchange\CheckoutController@getCustomerPoints');
        Route::post('/verify-last-exchange', 'Exchange\CheckoutController@verifyLastExchange');

        Route::post('/social-network-exchange', 'Exchange\ExchangePointsSmController@store'); 
        Route::post('/social-network-exchange/verify-link', 'Exchange\ExchangePointsSmController@verifyLink'); 
        
        Route::post('min/{priceMin}/max/{priceMax}/social-network-exchange/upload', 'Exchange\ExchangePointsSmController@uploadImage');
        Route::post('{categoryId}/social-network-exchange/upload', 'Exchange\ExchangePointsSmController@uploadImage');
       
    });
    
    Route::post('/checkin', 'CheckinController@store');
    
    Route::get('/survey', 'SurveyPostbackController@store');
    
    Route::get('/download', 'LoginController@downloadFile');
    
    Route::group(['prefix' => 'member-get-member'], function () {
        Route::group(['prefix' => 'email'], function () {
            Route::get('/send/{id}', 'MemberGetMemberController@sendEmail');
        });
    });

    Route::group(['prefix' => 'app-indications'], function () {
        Route::group(['prefix' => 'verify'], function () {
            Route::get('/hash', 'MobileApp\AppIndicationsController@verifyHash');
        });

        Route::group(['prefix' => 'download'], function () {
            Route::get('/{data}', 'MobileApp\AppIndicationsController@verifyIndicated');
        });
    });

    Route::group(['prefix' => 'social-class'], function () {
        Route::post('/search', 'SocialClass\SocialClassController@search');
        Route::post('/save', 'SocialClass\SocialClassController@store');
        Route::post('/checkout', 'SocialClass\SocialClassController@checkout');
    });
});