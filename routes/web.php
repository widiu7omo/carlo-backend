<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

Route::get('/', 'HomeController@Index')->name('main');
Route::post('/install', 'HomeController@install')->name('install');
Auth::routes(['register' => false, 'request' => false, 'reset' => false, 'email' => false]);

Route::get('faq', 'HomeController@Faq')->name('faq');
Route::get('terms', 'HomeController@Terms')->name('terms');
Route::get('privacy', 'HomeController@Privacy')->name('privacy');
Route::get('home', 'HomeController@Home')->name('home')->middleware('auth');

// pass reset
Route::get('login/forget', 'Auth\ResetPass@view')->name('forget');
Route::post('login/resetdata', 'Auth\ResetPass@reset')->name('reset');
Route::get('login/reset/{dta?}', 'Auth\ResetPass@makeReset')->where('dta', '(.*)');

Route::group(['middleware' => 'isAdmin'], function () {
    Route::get('index', 'Admin\Index@index')->name('index');

    // Members actions
    Route::get('members', 'Admin\Members@members')->name('members');
    Route::get('members/banned', 'Admin\Members@bannedMembers')->name('bannedmembers');
    Route::get('members/info', 'Admin\Members@userInfo')->name('userinfo');
    Route::post('members/info/update', 'Admin\Members@userinfoUpdate')->name('userinfo_update');
    Route::post('members/search', 'Admin\Members@memberSearch')->name('membersearch');
    Route::get('members/search', function () {
        return redirect()->route('members');
    });
    Route::post('members/ban', 'Admin\Members@newBan')->name('ban');
    Route::get('members/unban', 'Admin\Members@removeBan')->name('unban');
    Route::post('members/ban/edit', 'Admin\Members@editBan')->name('edit_ban');
    Route::post('members/reward', 'Admin\Members@Reward')->name('reward');
    Route::post('members/penalty', 'Admin\Members@Penalty')->name('penalty');

    // Notification
    Route::get('pushmsg', 'Admin\Notification@view')->name('push_msg');
    Route::post('pushmsg', 'Admin\Notification@view')->name('push_msg_post');
    Route::post('pushmsg/send', 'Admin\Notification@sendPush')->name('push_msg_send');

    Route::get('localmsg', 'Admin\Notification@localView')->name('local_msg');
    Route::post('localmsg/global', 'Admin\Notification@globalMsg')->name('global_msg_update');
    Route::post('localmsg/user', 'Admin\Notification@localSend')->name('local_msg_send');
    Route::get('localmsg/del', 'Admin\Notification@localDel')->name('local_msg_del');

    // History
    Route::get('history', 'Admin\History@view')->name('history');
    Route::get('history/search', 'Admin\History@search')->name('search_history');
    Route::get('history/del', 'Admin\History@del')->name('del_history');

    // Withdraw
    Route::get('withdraw', 'Admin\Withdraw@view')->name('withdraw');
    Route::get('withdraw/proceed', 'Admin\Withdraw@proceed')->name('withdraw_proceed');
    Route::post('withdraw/info', 'Admin\Withdraw@info')->name('withdraw_info');
    Route::post('withdraw/discard', 'Admin\Withdraw@discard')->name('withdraw_discard');

    // Gateway
    Route::get('gateways', 'Admin\Gateway@category')->name('gateway_category');
    Route::post('gateways/add', 'Admin\Gateway@categoryAdd')->name('gateway_category_add');
    Route::post('gateways/edit', 'Admin\Gateway@categoryEdit')->name('gateway_category_edit');
    Route::post('gateways/del', 'Admin\Gateway@categoryDel')->name('gateway_category_del');

    Route::get('gateway', 'Admin\Gateway@view')->name('gateway');
    Route::post('gateway/add', 'Admin\Gateway@add')->name('gateway_add');
    Route::post('gateway/edit', 'Admin\Gateway@edit')->name('gateway_edit');
    Route::get('gateway/del', 'Admin\Gateway@del')->name('gateway_del');

    Route::get('areward', 'Admin\Activityreward@view')->name('activity_reward');
    Route::post('areward/edit', 'Admin\Activityreward@Edit')->name('activity_reward_edit');
    Route::get('areward/toggle', 'Admin\Activityreward@Toggle')->name('activity_reward_toggle');

    // Frauds
    Route::get('frauds', 'Admin\Frauds@view')->name('frauds');
    Route::post('frauds/update', 'Admin\Frauds@update')->name('frauds_update');
    Route::get('frauds/clear', 'Admin\Frauds@clear')->name('frauds_clear');

    //Networks
    Route::get('networks/sdk', 'Admin\Networks@sdkView')->name('networks_sdk');
    Route::get('networks/sdk/edit', 'Admin\Networks@sdkEdit')->name('networks_sdk_edit');
    Route::post('networks/sdk/edit/update', 'Admin\Networks@sdkUpdate')->name('networks_sdk_update');

    Route::get('networks/cpa', 'Admin\Networks@cpaView')->name('networks_cpa');
    Route::get('networks/cpa/new', 'Admin\Networks@cpaNew')->name('networks_cpa_new');
    Route::post('networks/cpa/new/add', 'Admin\Networks@cpaAdd')->name('networks_cpa_add');
    Route::get('networks/cpa/edit', 'Admin\Networks@cpaEdit')->name('networks_cpa_edit');
    Route::post('networks/cpa/edit/update', 'Admin\Networks@cpaUpdate')->name('networks_cpa_update');
    Route::get('networks/cpa/del', 'Admin\Networks@offerwallDel')->name('networks_cpa_del');

    Route::get('networks/cpv', 'Admin\Networks@cpvView')->name('networks_cpv');

    Route::get('networks/custom', 'Admin\Networks@customView')->name('networks_custom');
    Route::post('networks/custom/postback', 'Admin\Networks@customPostback')->name('networks_custom_postback');
    Route::get('networks/custom/add', 'Admin\Networks@customAddView')->name('networks_custom_new');
    Route::post('networks/custom/add', 'Admin\Networks@customAdd')->name('networks_custom_add');
    Route::get('networks/custom/edit', 'Admin\Networks@customEditView')->name('networks_custom_edit');
    Route::post('networks/custom/edit', 'Admin\Networks@customEdit')->name('networks_custom_update');
    Route::get('networks/custom/del', 'Admin\Networks@customDel')->name('networks_custom_del');

    Route::get('networks/yt', 'Admin\Networks@youtubeView')->name('networks_youtube');
    Route::post('networks/yt/add', 'Admin\Networks@youtubeAdd')->name('networks_youtube_add');
    Route::post('networks/yt/del', 'Admin\Networks@youtubeDel')->name('networks_youtube_del');

    Route::get('networks/ppv', 'Admin\Networks@ppvView')->name('networks_ppv');
    Route::post('networks/ppv/add', 'Admin\Networks@ppvAdd')->name('networks_ppv_add');
    Route::post('networks/ppv/del', 'Admin\Networks@ppvDel')->name('networks_ppv_del');

    Route::get('networks/web', 'Admin\Networks@webView')->name('networks_web');
    Route::get('networks/web/new', 'Admin\Networks@webNew')->name('networks_web_new');
    Route::post('networks/web/new/add', 'Admin\Networks@webAdd')->name('networks_web_add');
    Route::get('networks/web/edit', 'Admin\Networks@webEdit')->name('networks_web_edit');
    Route::post('networks/web/edit/update', 'Admin\Networks@webUpdate')->name('networks_web_update');

    // Maintanance
    Route::get('maintain', 'Admin\Maintain@view')->name('maintain');
    Route::get('faq/view', 'Admin\Maintain@faq')->name('faq_admin');
    Route::post('faq/add', 'Admin\Maintain@faqAdd')->name('faq_add');
    Route::get('faq/del', 'Admin\Maintain@faqDel')->name('faq_del');
    Route::post('tos/update', 'Admin\Maintain@tosUpdate')->name('tos_update');
    Route::post('privacy/update', 'Admin\Maintain@privacyUpdate')->name('privacy_update');
    Route::get('app/update', 'Admin\Maintain@appUpdate')->name('app_update');
    Route::post('app/userupdate', 'Admin\Maintain@userUpdate')->name('user_update');

    // Settings
    Route::get('settings', 'Admin\Settings@view')->name('settings');
    Route::get('settings/leaderboard', 'Admin\Settings@updateLB')->name('settings_update_lb');
    Route::post('settings/update', 'Admin\Settings@update')->name('settings_update');
    Route::get('clear/dash', 'Admin\Index@clearDash')->name('clear_dash');
    Route::post('clear/system', 'Admin\Settings@clearCache')->name('clear_system');
    Route::post('settings/update/email', 'Admin\Settings@emailUpdate')->name('email_update');


    // Support
    Route::get('support', 'Admin\Support@view')->name('support');
    Route::post('support/chat', 'Admin\Support@chat')->name('support_chat');
    Route::post('support/mark', 'Admin\Support@mark')->name('support_mark');
    Route::post('support/send', 'Admin\Support@send')->name('support_send');
    Route::post('support/df', 'Admin\Support@delFull')->name('support_del_full');
    Route::post('support/ds', 'Admin\Support@delSingle')->name('support_del_single');

    // Games
    Route::get('game/quiz/category', 'Admin\GameQuiz@quizCategory')->name('game_quiz_category');
    Route::post('game/quiz/category/add', 'Admin\GameQuiz@quizAddCategory')->name('game_quiz_category_add');
    Route::post('game/quiz/category/edit', 'Admin\GameQuiz@quizEditCategory')->name('game_quiz_category_edit');
    Route::post('game/quiz/category/del', 'Admin\GameQuiz@quizDelCategory')->name('game_quiz_category_del');
    Route::post('game/quiz/settings', 'Admin\GameQuiz@updateSettings')->name('game_quiz_settings_update');
    Route::get('game/quiz', 'Admin\GameQuiz@quizView')->name('game_quiz');
    Route::post('game/quiz/add', 'Admin\GameQuiz@quizAdd')->name('game_quiz_add');
    Route::post('game/quiz/edit', 'Admin\GameQuiz@quizEdit')->name('game_quiz_edit');
    Route::post('game/quiz/del', 'Admin\GameQuiz@quizDel')->name('game_quiz_del');

    Route::get('game/tournament', 'Admin\GameTour@view')->name('game_tour');
    Route::post('game/tournament/sett', 'Admin\GameTour@sett')->name('game_tour_sett');
    Route::get('game/tournament/winner/form', 'Admin\GameTour@winnerForm')->name('game_tour_winner_form');
    Route::post('game/tournament/winner/update', 'Admin\GameTour@winnerUpdate')->name('game_tour_winner_update');
    Route::post('game/tournament/qs/add', 'Admin\GameTour@qsAdd')->name('game_tour_qs_add');
    Route::post('game/tournament/qs/del', 'Admin\GameTour@qsDel')->name('game_tour_qs_del');

    Route::get('game/guessword', 'Admin\GameGuessWord@view')->name('game_guess_word');
    Route::post('game/guessword/add', 'Admin\GameGuessWord@add')->name('game_guess_word_add');
    Route::post('game/guessword/edit', 'Admin\GameGuessWord@edit')->name('game_guess_word_edit');
    Route::post('game/guessword/del', 'Admin\GameGuessWord@del')->name('game_guess_word_del');
    Route::post('game/guessword/sett', 'Admin\GameGuessWord@settUpdate')->name('game_guess_word_settings_update');

    Route::get('game/ip/category', 'Admin\GameIP@IPCategory')->name('game_ip_category');
    Route::post('game/ip/category/add', 'Admin\GameIP@IPAddCategory')->name('game_ip_category_add');
    Route::post('game/ip/category/edit', 'Admin\GameIP@IPEditCategory')->name('game_ip_category_edit');
    Route::post('game/ip/category/del', 'Admin\GameIP@IPDelCategory')->name('game_ip_category_del');
    Route::post('game/ip/settings', 'Admin\GameIP@updateSettings')->name('game_ip_settings_update');
    Route::get('game/ip', 'Admin\GameIP@IPView')->name('game_ip');
    Route::post('game/ip/add', 'Admin\GameIP@IPAdd')->name('game_ip_add');
    Route::post('game/ip/edit', 'Admin\GameIP@IPEdit')->name('game_ip_edit');
    Route::post('game/ip/del', 'Admin\GameIP@IPDel')->name('game_ip_del');

    Route::get('game/jpz/category', 'Admin\GameJPZ@JPZCategory')->name('game_jpz_category');
    Route::post('game/jpz/category/add', 'Admin\GameJPZ@JPZAddCategory')->name('game_jpz_category_add');
    Route::post('game/jpz/category/edit', 'Admin\GameJPZ@JPZEditCategory')->name('game_jpz_category_edit');
    Route::post('game/jpz/category/del', 'Admin\GameJPZ@JPZDelCategory')->name('game_jpz_category_del');
    Route::post('game/jpz/settings', 'Admin\GameJPZ@updateSettings')->name('game_jpz_settings_update');
    Route::get('game/jpz', 'Admin\GameJPZ@JPZView')->name('game_jpz');
    Route::post('game/jpz/add', 'Admin\GameJPZ@JPZAdd')->name('game_jpz_add');
    Route::post('game/jpz/edit', 'Admin\GameJPZ@JPZEdit')->name('game_jpz_edit');
    Route::post('game/jpz/del', 'Admin\GameJPZ@JPZDel')->name('game_jpz_del');

    Route::get('game/wheel', 'Admin\GameWheel@view')->name('game_wheel');
    Route::post('game/wheel/settings', 'Admin\GameWheel@settings')->name('game_wheel_settings');
    Route::post('game/wheel/add', 'Admin\GameWheel@add')->name('game_wheel_add');
    Route::post('game/wheel/edit', 'Admin\GameWheel@edit')->name('game_wheel_edit');
    Route::post('game/wheel/del', 'Admin\GameWheel@del')->name('game_wheel_del');
    Route::get('game/wheel/replace', 'Admin\GameWheel@replace')->name('game_wheel_replace');

    Route::get('game/slot', 'Admin\GameSlot@view')->name('game_slot');
    Route::post('game/slot/update', 'Admin\GameSlot@update')->name('game_slot_update');
    Route::post('game/slot/addline', 'Admin\GameSlot@updateLine')->name('game_slot_line_add');

    Route::get('game/html', 'Admin\GameHtml@view')->name('game_html');
    Route::post('game/html/add/file', 'Admin\GameHtml@addFile')->name('game_html_file_add');
    Route::post('game/html/add/image', 'Admin\GameHtml@addImage')->name('game_html_image_add');
    Route::post('game/html/del/file', 'Admin\GameHtml@delFile')->name('game_html_file_del');
    Route::post('game/html/del/image', 'Admin\GameHtml@delImage')->name('game_html_image_del');
    Route::post('game/html/add/game', 'Admin\GameHtml@addGame')->name('game_html_game');
    Route::get('game/html/del/game', 'Admin\GameHtml@delGame')->name('game_html_game_del');
    Route::post('game/html/cache', 'Admin\GameHtml@cacheTime')->name('game_html_cache');
    Route::post('game/html/rewards', 'Admin\GameHtml@setRwd')->name('game_html_rewards');

    Route::get('game/lotto', 'Admin\GameLotto@view')->name('game_lotto');
    Route::post('game/lotto/update', 'Admin\GameLotto@update')->name('game_lotto_update');
    Route::post('game/lotto/addwinner', 'Admin\GameLotto@addWinner')->name('game_lotto_addwinner');
    Route::get('game/lotto/delwinner', 'Admin\GameLotto@delWinner')->name('game_lotto_delwinner');

    Route::get('game/scratch', 'Admin\GameScratch@catView')->name('game_scratch_cat');
    Route::get('game/scratcher', 'Admin\GameScratch@view')->name('game_scratcher');
    Route::post('game/scratcher/make', 'Admin\GameScratch@make')->name('game_scratcher_make');
    Route::get('game/scratcher/del', 'Admin\GameScratch@del')->name('game_scratcher_del');
    Route::get('game/scratcher/clean', 'Admin\GameScratch@cleanUp')->name('game_scratcher_clean');

    // Admins
    Route::get('member/admin/sett', 'Admin\Admins@view')->name('admins');
    Route::post('member/admin/sett/update', 'Admin\Admins@update')->name('admins_update');
    Route::post('member/admin/sett/change', 'Admin\Admins@change')->name('admins_change');
    Route::post('member/admin/note/save', 'Admin\Admins@saveNote')->name('save_admin_note');

    // Geoloaction API
    Route::get('geo/api', 'Admin\Settings@geoApi')->name('geo_api');
    Route::post('geo/api/update', 'Admin\Settings@geoUpdate')->name('geo_api_update');
});
