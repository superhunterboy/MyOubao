<?php

/**
 * 支付渠道管理
 */
$sUrlDir = 'payment-accounts';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'payment-accounts';
    $controller = 'PaymentAccountController';
    $prev = $controller . '@';
    Route::get('/index', array('as' => $resource . '.index', 'uses' => $prev . 'index'));
    Route::any('create/{id}', array('as' => $resource . '.create', 'uses' => $prev . 'create'));
    Route::get('{id}/view', array('as' => $resource . '.view', 'uses' => $prev . 'view'));
    Route::get('{id}/default', array('as' => $resource . '.default', 'uses' => $prev . 'setDefault'));
    Route::get('{id}/close', array('as' => $resource . '.close', 'uses' => $prev . 'close'));
    Route::get('{id}/open', array('as' => $resource . '.open', 'uses' => $prev . 'open'));
    Route::any('{id}/edit', array('as' => $resource . '.edit', 'uses' => $prev . 'edit')); // ->before('not.self');
    Route::delete('{id}', array('as' => $resource . '.destroy', 'uses' => $prev . 'destroy')); // ->before('not.self');
});
