<?php

# 充值管理
Route::group(['prefix' => 'deposit'], function () {
    $resource = 'user-recharges';
    $controller = 'UserDepositController@';
    Route::get('/', ['as' => $resource . '.index', 'uses' => $controller . 'index']);
    Route::any('create', ['as' => $resource . '.create', 'uses' => $controller . 'create']);
    Route::get('{id}/view', ['as' => $resource . '.view', 'uses' => $controller . 'view']);
    Route::any('{id}/edit', ['as' => $resource . '.edit', 'uses' => $controller . 'edit']);
    Route::delete('{id}', ['as' => $resource . '.destroy', 'uses' => $controller . 'destroy']);
    Route::any('netbank', ['as' => $resource . '.netbank', 'uses' => $controller . 'netbank']);
    Route::any('quick/{id?}', ['as' => $resource . '.quick', 'uses' => $controller . 'quick']);
//    Route::post( 'confirm', ['as' => $resource . '.confirm', 'uses' => $controller . 'confirm']);
});
