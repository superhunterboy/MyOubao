<?php

/**
 * 支付渠道管理
 */
$sUrlDir = 'payment-platforms';
Route::group(['prefix' => $sUrlDir], function () {
    $resource   = 'payment-platforms';
    $controller = 'PaymentPlatformController@';
    Route::get('/index', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::any('create/{id}',array('as' => $resource . '.create','uses' => $controller . 'create'));
    Route::get('{id}/view', array('as' => $resource . '.view' , 'uses' => $controller . 'view'   ));
    Route::get('{id}/default', array('as' => $resource . '.default' , 'uses' => $controller . 'setDefault'   ));
    Route::any('{id}/edit', array('as' => $resource . '.edit', 'uses' => $controller . 'edit')); // ->before('not.self');
    Route::delete('{id}', array('as' => $resource . '.destroy', 'uses' => $controller . 'destroy')); // ->before('not.self');
});
