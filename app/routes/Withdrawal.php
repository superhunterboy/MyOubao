<?php

/**
 * 提现记录
 */
$sUrlDir = 'withdrawals';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'withdrawals';
    $controller = 'WithdrawalController@';

    Route::get('/', ['as' => $resource . '.index', 'uses' => $controller . 'index']);
    Route::get('verified', ['as' => $resource . '.verified', 'uses' => $controller . 'verifiedRecords']);
    Route::get('unverified', ['as' => $resource . '.unverified', 'uses' => $controller . 'unVefiriedRecords']);
    Route::get('remit', ['as' => $resource . '.remit', 'uses' => $controller . 'remitRecords']);
    Route::any('create', ['as' => $resource . '.create', 'uses' => $controller . 'create']);
    Route::get('{id}/view', ['as' => $resource . '.view', 'uses' => $controller . 'view']);
    Route::any('{id}/edit', ['as' => $resource . '.edit', 'uses' => $controller . 'edit']);
    Route::delete('{id}', ['as' => $resource . '.destroy', 'uses' => $controller . 'destroy']);
    Route::any('{id}/verify', ['as' => $resource . '.verify', 'uses' => $controller . 'verify']);
    Route::any('{id}/refuse', ['as' => $resource . '.refuse', 'uses' => $controller . 'refuse']);
    Route::get('{id}/accept-verify', ['as' => $resource . '.accept-verify', 'uses' => $controller . 'acceptVerify']);
    Route::any('{id}/remit-verify', ['as' => $resource . '.remit-verify', 'uses' => $controller . 'remittanceVerify']);
    Route::get('{id}/accept-withdrawal', ['as' => $resource . '.accept-withdrawal', 'uses' => $controller . 'acceptWithdrawal']);
    Route::any('{id}/submit-document', ['as' => $resource . '.submit-document', 'uses' => $controller . 'setToSuccess']);
    Route::any('{id}/verify-deduct', ['as' => $resource . '.verify-deduct', 'uses' => $controller . 'verifyDeduct']);
    Route::get('load-img/{id}', ['as' => $resource . '.load-img', 'uses' => $controller . 'loadImg']);
    Route::get('/download', ['as' => $resource . '.download', 'uses' => $controller . 'download']);
});
