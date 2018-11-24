<?php

# 账变管理
Route::group(['prefix' => 'user-transactions'], function () {
    $resource = 'user-transactions';
    $controller = 'User_TransactionController@';
    Route::any( '/{id?}', ['as' => $resource . '.index', 'uses' => $controller . 'index']);
    Route::get( '/{id?}/mydeposit', ['as' => $resource . '.mydeposit', 'uses' => $controller . 'myDeposit']);
    Route::get( '/{id?}/mywithdraw', ['as' => $resource . '.mywithdraw', 'uses' => $controller . 'myWithdraw']);
    Route::get( '/{id?}/mytransfer', ['as' => $resource . '.mytransfer', 'uses' => $controller . 'myTransfer']);
    Route::get( '/{id?}/mycommission', ['as' => $resource . '.mycommission', 'uses' => $controller . 'myCommission']);
   
    Route::any( 'create', ['as' => $resource . '.create', 'uses' => $controller . 'create']);
    Route::get('{id}/view', ['as' => $resource . '.view', 'uses' => $controller . 'view']);
    Route::any('{id}/edit', ['as' => $resource . '.edit', 'uses' => $controller . 'edit']);
    Route::delete( '{id}', ['as' => $resource . '.destroy', 'uses' => $controller . 'destroy']);
});