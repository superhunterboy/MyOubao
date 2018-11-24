<?php

/**
 * Transaction管理
 */
$sUrlDir = 'transaction-histories';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'transaction-histories';
    $controller = 'TransactionHistoryController@';
    Route::get('/', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::get(           '/download', ['as' => $resource . '.download',   'uses' => $controller . 'download']);
});
