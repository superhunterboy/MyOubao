<?php

/**
 * Account管理
 */
$sUrlDir = 'pay-channels';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'pay-channels';
    $controller = 'PayChannelController@';
    Route::any(      '/index', ['as' => $resource . '.index',   'uses' => $controller . 'index']);
//    Route::get(   '{id}/view', ['as' => $resource . '.view',    'uses' => $controller . 'view']);
});
