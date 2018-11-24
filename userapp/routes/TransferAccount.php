<?php

# 转账
Route::group(['prefix' => 'user-transfers'], function () {
    $resource = 'user-transfers';
    $controller = 'UserTransferController@';
    Route::get( '/index/{id?}', ['as' => $resource . '.index', 'uses' => $controller . 'index']);
    Route::any('transfer-to-sub', ['as' => $resource . '.transfer-to-sub',      'uses' => $controller . 'transferToSub']);
});