<?php

/**
 * Transaction管理
 */
$sUrlDir = 'deposit-histories';
Route::group(['prefix' => $sUrlDir], function () {

    $resource = 'deposit-histories';
    $controller = 'DepositHistoryController@';
    Route::get(        '/', ['as' => $resource . '.index',    'uses' => $controller . 'index']);
    Route::get('{id}/view', ['as' => $resource . '.view',     'uses' => $controller . 'view']);

    Route::get('/download', ['as' => $resource . '.download', 'uses' => $controller . 'download']);
});
