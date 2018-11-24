<?php

/**
 * commission-settings管理
 */
$sUrlDir = 'commission-settings';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'commission-settings';
    $controller = 'CommissionSettingController@';
    Route::get('/index', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
});
