<?php

/**
 * UserProfit 用户销量统计
 */
$sUrlDir = 'stat-lottery-profits';
Route::group(['prefix' => $sUrlDir], function () {
    $resource   = 'lottery-profits';
    $controller = 'LotteryProfitController@';
    Route::get(           '/', ['as' => $resource . '.index',   'uses' => $controller . 'index']);
    // Route::any('create/{id?}', ['as' => $resource . '.create',  'uses' => $controller . 'create']);
     Route::get(   '{id}/view', ['as' => $resource . '.view',    'uses' => $controller . 'view']);
    // Route::any(   '{id}/edit', ['as' => $resource . '.edit',    'uses' => $controller . 'edit']);
    // Route::delete(     '{id}', ['as' => $resource . '.destroy', 'uses' => $controller . 'destroy']);
    Route::get(    '/download', ['as' => $resource . '.download',   'uses' => $controller . 'download']);
});
