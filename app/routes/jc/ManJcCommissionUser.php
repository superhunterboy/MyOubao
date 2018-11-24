<?php

/**
 * commission-settings管理
 */
$sUrlDir = 'jc-commission-settings';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'jc-commission-settings';
    $controller = 'JcController\CommissionSettingController@';
    Route::any('{id}/settings', array('as' => $resource . '.settings', 'uses' => $controller . 'setting'));
});
