<?php


Route::group(['prefix' => 'sdpay-deposit-settings'], function () {
    Route::any('settings', array('as' => 'sdpay-deposit-settings.settings', 'uses' => 'SdpayDepositSettingController@settings'));
});
