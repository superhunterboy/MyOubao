<?php

/**
 * Bank管理
 */
$sUrlDir = 'deposits';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'deposits';
    $controller = 'DepositController@';
    Route::get('/', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::any('create', array('as' => $resource . '.create', 'uses' => $controller . 'create'));
    Route::get('{id}/view', array('as' => $resource . '.view', 'uses' => $controller . 'view'));
    Route::any('{id}/edit', array('as' => $resource . '.edit', 'uses' => $controller . 'edit')); // ->before('not.self');
    Route::delete('{id}', array('as' => $resource . '.destroy', 'uses' => $controller . 'destroy')); // ->before('not.self');
    Route::get('/download', ['as' => $resource . '.download', 'uses' => $controller . 'download']);
    Route::get('{id}/check', ['as' => $resource . '.check', 'uses' => $controller . 'check']);
    Route::get('{id}/accept', ['as' => $resource . '.accept', 'uses' => $controller . 'accept']);
    Route::get('{id}/accept-verify', ['as' => $resource . '.accept-verify', 'uses' => $controller . 'acceptVerify']);
    Route::any('{id}/set-wait-load', ['as' => $resource . '.set-wait-load', 'uses' => $controller . 'setToWaitLoad']);
    Route::any('{id}/set-wait-verify', ['as' => $resource . '.set-wait-verify', 'uses' => $controller . 'setToWaitVerify']);
    Route::get('{id}/set-failed', ['as' => $resource . '.fail', 'uses' => $controller . 'setFailed']);
    Route::get('{id}/set-reject', ['as' => $resource . '.reject', 'uses' => $controller . 'setReject']);
    Route::get('{id}/set-send-commission', ['as' => $resource . '.set-send-commission', 'uses' => $controller . 'setCommissionTask']);
    Route::get('{id}/set-deposit-task', ['as' => $resource . '.set-deposit-task', 'uses' => $controller . 'createDepositTask']);
    Route::get('{id}/set-exception', ['as' => $resource . '.set-exception', 'uses' => $controller . 'setException']);
    Route::get('load-img/{id}/{imgName}', ['as' => $resource . '.load-img', 'uses' => $controller . 'loadImg']);
});
