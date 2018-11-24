<?php

/**
 * Jackpots管理
 */
$sUrlDir = 'jackpots';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'jackpots';
    $controller = 'JackpotsController@';
    Route::get(      '/index', ['as' => $resource . '.index',   'uses' => $controller . 'index']);
    Route::get(   '{id}/view', ['as' => $resource . '.view',    'uses' => $controller . 'view']);
    Route::any(   '/create', ['as' => $resource . '.create',    'uses' => $controller . 'create']);
    Route::any(   '{id}/edit', ['as' => $resource . '.edit',    'uses' => $controller . 'edit']);
    Route::any(   '{id}', ['as' => $resource . '.destroy',    'uses' => $controller . 'destroy']);
});
