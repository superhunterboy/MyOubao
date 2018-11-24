<?php

/**
 * 提现记录
 */
$sUrlDir = 'withdrawal-histories';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'withdrawal-histories';
    $controller = 'WithdrawalHistoryController@';

    Route::get(                 '/', ['as' => $resource . '.index',         'uses' => $controller . 'index']);
    Route::get(          'verified', ['as' => $resource . '.verified',      'uses' => $controller . 'verifiedRecords']);
    Route::get(        'unverified', ['as' => $resource . '.unverified',    'uses' => $controller . 'unVefiriedRecords']);
    Route::any(            'create', ['as' => $resource . '.create',        'uses' => $controller . 'create']);
    Route::get(         '{id}/view', ['as' => $resource . '.view',          'uses' => $controller . 'view']);
    Route::any(         '{id}/edit', ['as' => $resource . '.edit',          'uses' => $controller . 'edit']);
    Route::delete(           '{id}', ['as' => $resource . '.destroy',       'uses' => $controller . 'destroy']);
    Route::get(        '{id}/claim', ['as' => $resource . '.claim',         'uses' => $controller . 'claim']);
    Route::get(       '{id}/verify', ['as' => $resource . '.verify',        'uses' => $controller . 'verify']);
    Route::any(       '{id}/refuse', ['as' => $resource . '.refuse',        'uses' => $controller . 'refuse']);
    Route::any(      '{id}/waiting', ['as' => $resource . '.waiting',       'uses' => $controller . 'waitingForConfirmation']);
    Route::get('{id}/manualsuccess', ['as' => $resource . '.manualsuccess', 'uses' => $controller . 'manualSetToSuccess']);
    Route::get('{id}/manualfailure', ['as' => $resource . '.manualfailure', 'uses' => $controller . 'manualSetToFailure']);
    Route::get(         '/download', ['as' => $resource . '.download',      'uses' => $controller . 'download']);
});
