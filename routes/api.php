<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('loginerror', function (Request $req) {
    return ['status' => -1, 'message' => 'You need to login first!'];
})->name('apiloginerror');

Route::group(['middleware' => 'api'], function () {
    Route::post('/geo', 'Connect@geo');
    Route::post('/connect', 'Connect@check');
    Route::post('/login', 'Auth\APILoginController@login');
    Route::post('/flogin', 'Auth\APILoginController@fLogin');
    Route::post('/glogin', 'Auth\APILoginController@gLogin');
    Route::post('/plogin', 'Auth\APILoginController@pLogin');
    Route::post('/register', 'Auth\RegisterController@apiCreate');
    Route::post('/forget', 'Auth\ResetPass@apiReset');

    // Postbacks
    Route::get('pb/{net}', 'Postback@cpa')->where('net', '^[\w ]+$');
    Route::post('pb/{net}', 'Postback@cpa')->where('net', '^[\w ]+$');
    Route::get('cpb', 'Postback@premiumUrlOffers');
    Route::post('cpb', 'Postback@premiumUrlOffers');

    Route::get('/game/html/image', 'User\HtmlGame@image')->name('img_api');
    Route::post('/support/tos', 'User\Misc@tos');

    //Route::get('/game/slot/test', 'User\Slot@post');
    //Route::get('/game/slot/get', 'User\Slot@get');
});
Route::group(['middleware' => ['api', 'securedAPI']], function () {
    Route::post('/me/info', 'User\Userinfo@info');
    Route::post('/me/fid', 'User\Userinfo@fid');
    Route::post('/me/balance', 'User\Userinfo@balance');
    Route::post('/profile', 'User\Userinfo@profile');
    Route::post('/ref', 'User\Userinfo@refView');
    Route::post('/abnr', 'User\Userinfo@autobanRoot');
    Route::post('/vm', 'User\Userinfo@vpnMonitor');
    Route::post('/profile/update', 'User\Userinfo@profileChange');

    Route::post('/offers/premium', 'User\Offers@servePremium');
    Route::post('/offers/ppv', 'User\Offers@servePpv');
    Route::post('/offers/yt', 'User\Offers@serveYt');
    Route::post('/offers/yt/reward', 'User\Offers@rewardYt');
    Route::post('/offers/cpa/done', 'User\Offers@completedCpa');
    Route::post('/offers/drwd', 'User\Offers@playReward');
    Route::post('/rank', 'User\Misc@ranking');

    //Support
    Route::post('/support/get', 'User\Support@get');
    Route::post('/support/post', 'User\Support@post');
    Route::post('/support/faq', 'User\Misc@faq');

    //Withdrawals
    Route::post('/gift/get', 'User\Withdrawal@get');
    Route::post('/gift/post', 'User\Withdrawal@post');

    //History
    Route::post('/history/ref', 'User\Userinfo@refHistory');

    //Games
    Route::post('/game/ar/get', 'User\Userinfo@arGet');
    Route::post('/game/ar/reward', 'User\Userinfo@arReward');

    Route::post('/game/quiz/cat', 'User\Quiz@Categories');
    Route::post('/game/quiz/info', 'User\Quiz@catInfo');
    Route::post('/game/quiz/get', 'User\Quiz@quiz');
    Route::post('/game/quiz/fifty', 'User\Quiz@Fifty');
    Route::post('/game/quiz/reward', 'User\Quiz@reward');
    Route::post('/game/quiz/purchase', 'User\Quiz@purchase');

    Route::post('/game/gw/info', 'User\GuessWord@info');
    Route::post('/game/gw/get', 'User\GuessWord@get');
    Route::post('/game/gw/reward', 'User\GuessWord@reward');
    Route::post('/game/gw/hint', 'User\GuessWord@hint');
    Route::post('/game/gw/solve', 'User\GuessWord@solve');
    
    Route::post('/game/ip/cat', 'User\ImagePuzzle@cat');
    Route::post('/game/ip/get', 'User\ImagePuzzle@get');
    Route::post('/game/ip/reward', 'User\ImagePuzzle@reward');

    Route::post('/game/jpz/cat', 'User\JigsawPuzzle@cat');
    Route::post('/game/jpz/get', 'User\JigsawPuzzle@get');
    Route::post('/game/jpz/reward', 'User\JigsawPuzzle@reward');
    Route::post('/game/jpz/help', 'User\JigsawPuzzle@help');

    Route::post('/game/t/enroll', 'User\Tournament@enroll');
    Route::post('/game/t/get', 'User\Tournament@get');
    Route::post('/game/t/ans', 'User\Tournament@ans');
    Route::post('/game/t/rank', 'User\Tournament@rank');

    Route::post('/game/wheel/get', 'User\Wheel@get');
    Route::post('/game/wheel/post', 'User\Wheel@post');

    Route::post('/game/slot/get', 'User\Slot@get');
    Route::post('/game/slot/post', 'User\Slot@post');

    Route::post('/game/lotto/get', 'User\Lotto@get');
    Route::post('/game/lotto/post', 'User\Lotto@post');
    Route::post('/game/lotto/history', 'User\Lotto@history');

    Route::post('/game/scratcher/cat', 'User\Scratcher@cat');
    Route::post('/game/scratcher/result', 'User\Scratcher@result');
    Route::post('/game/scratcher/purchase', 'User\Scratcher@purchase');

    //Game DLs
    Route::post('/game/html/get', 'User\HtmlGame@get');
	Route::post('/game/html/counter', 'User\HtmlGame@rwd');
    Route::post('/game/html/pack', 'User\HtmlGame@pack');
});
