<?php

/**
 * Category管理
 */
$sUrlDir = 'cms-categories';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'cms-categories';
    $controller = 'CmsCategoryController@';
    Route::get(        '/', ['as' => $resource . '.index',   'uses' => $controller . 'index']);
    Route::any(   'create', ['as' => $resource . '.create',  'uses' => $controller . 'create']);
    Route::get('{id}/view', ['as' => $resource . '.view',    'uses' => $controller . 'view']);
    Route::any('{id}/edit', ['as' => $resource . '.edit',    'uses' => $controller . 'edit']);
    Route::delete(  '{id}', ['as' => $resource . '.destroy', 'uses' => $controller . 'destroy']);
});



