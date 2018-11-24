<?php


Route::group(['prefix' => 'withdrawal-channel-settings'], function () {
    Route::any('settings', array('as' => 'withdrawal-channel-settings.settings', 'uses' => 'WithdrawalChannelSettingController@settings'));
});
