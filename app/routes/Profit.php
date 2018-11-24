<?php

/**
 * UserProfit 用户销量统计
 */
$sUrlDir = 'stat-profits';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'profits';
    $controller = 'ProfitController@';
    Route::get(           '/', ['as' => $resource . '.index',   'uses' => $controller . 'index']);
    Route::get(           '/graphindex', ['as' => $resource . '.graphindex',   'uses' => $controller . 'graphIndex']);
    // Route::any('create/{id?}', ['as' => $resource . '.create',  'uses' => $controller . 'create']);
     Route::get(   '{id}/view', ['as' => $resource . '.view',    'uses' => $controller . 'view']);
    // Route::any(   '{id}/edit', ['as' => $resource . '.edit',    'uses' => $controller . 'edit']);
    // Route::delete(     '{id}', ['as' => $resource . '.destroy', 'uses' => $controller . 'destroy']);
});
