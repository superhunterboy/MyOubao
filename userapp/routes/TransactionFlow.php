<?php

# 流水管理
Route::group(['prefix' => 'transaction-flow'], function () {
    
    $resource = 'transaction-flow';
    $controller = 'TransactionFlowController@';
    
    Route::get('/', ['as' => $resource.'.index','uses' => $controller.'index']);

    /*
	    Route::get( '/{id?}/mydeposit', ['as' => $resource . '.mydeposit', 'uses' => $controller . 'myDeposit']);
	    Route::get( '/{id?}/mywithdraw', ['as' => $resource . '.mywithdraw', 'uses' => $controller . 'myWithdraw']);
	    Route::any( 'create', ['as' => $resource . '.create', 'uses' => $controller . 'create']);
	    Route::get('{id}/view', ['as' => $resource . '.view', 'uses' => $controller . 'view']);
    */
    /*Route::any('{id}/edit', ['as' => $resource . '.edit', 'uses' => $controller . 'edit']);
    Route::delete( '{id}', ['as' => $resource . '.destroy', 'uses' => $controller . 'destroy']);*/

});