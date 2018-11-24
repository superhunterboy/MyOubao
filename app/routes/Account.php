<?php

/**
 * Account管理
 */
$sUrlDir = 'accounts';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'accounts';
    $controller = 'AccountController@';
    Route::get('/index', ['as' => $resource . '.index', 'uses' => $controller . 'index']);
    Route::get('{id}/view', ['as' => $resource . '.view', 'uses' => $controller . 'view']);
    Route::get('{id}/set-account', ['as' => $resource . '.set-account', 'uses' => $controller . 'setAccount']);
});
