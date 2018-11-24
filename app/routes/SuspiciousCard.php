<?php

/**
 * Account管理
 */
$sUrlDir = 'suspicious-cards';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'suspicious-cards';
    $controller = 'SuspiciousCardController@';
    Route::get(      '/index', ['as' => $resource . '.index',   'uses' => $controller . 'index']);
    Route::any(   '/create', ['as' => $resource . '.create',    'uses' => $controller . 'create']);
    Route::any(   '{id}/edit', ['as' => $resource . '.edit',    'uses' => $controller . 'edit']);
    Route::get(   '{id}/view', ['as' => $resource . '.view',    'uses' => $controller . 'view']);
    Route::delete(         '{id}', ['as' => $resource . '.destroy', 'uses' => $controller . 'destroy']);
});
