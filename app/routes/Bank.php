<?php

/**
 * Bank管理
 */
$sUrlDir = 'banks';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'banks';
    $controller = 'BankController@';
    Route::get( '/', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::any( 'create', array('as' => $resource . '.create', 'uses' => $controller . 'create'));
    Route::get('{id}/view', array('as' => $resource . '.view' , 'uses' => $controller . 'view'   ));
    Route::any('{id}/edit', array('as' => $resource . '.edit', 'uses' => $controller . 'edit')); // ->before('not.self');
    Route::delete('{id}', array('as' => $resource . '.destroy', 'uses' => $controller . 'destroy')); // ->before('not.self');
    Route::get('{id}/fee/view', array('as' => $resource . '.fee_view' , 'uses' => $controller . 'feeView'   ));
    Route::any('{id}/fee/edit', array('as' => $resource . '.fee_edit', 'uses' => $controller . 'feeEdit'));
    Route::get('prohibitedWithdraw', array('as' => $resource . '.prohibitedWithdraw' , 'uses' => $controller . 'prohibitedWithdraw'   ));
    Route::get('withdrawal-channel', array('as' => $resource . '.withdrawalChannel' , 'uses' => $controller . 'withdrawalChannel'   ));
});
