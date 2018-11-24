<?php

/**
 * Account管理
 */
$sUrlDir = 'game-menus';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'game-menus';
    $controller = 'GameMenuController@';
    Route::get(      '/index', ['as' => $resource . '.index',   'uses' => $controller . 'index']);
    Route::get(   '{id}/view', ['as' => $resource . '.view',    'uses' => $controller . 'view']);
    Route::any(   '{id}/edit', ['as' => $resource . '.edit',    'uses' => $controller . 'edit']);
    Route::any(   '/create', ['as' => $resource . '.create',    'uses' => $controller . 'create']);
     Route::delete('{id}', array('as' => $resource . '.destroy', 'uses' => $controller . 'destroy')); // ->before('not.self');
    Route::post('set-order', array('as' => $resource . '.set-order', 'uses' => $controller . 'setOrder'));
});
