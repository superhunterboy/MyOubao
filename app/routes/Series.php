<?php

/**
 * 系列管理
 */
$sUrlDir = 'series';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'series';
    $controller = 'SeriesController@';
    Route::get( '/', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::any( 'create', array('as' => $resource . '.create', 'uses' => $controller . 'create'));
    Route::get('{id}/view', array('as' => $resource . '.view' , 'uses' => $controller . 'view'   ));
    Route::any('{id}/edit', array('as' => $resource . '.edit', 'uses' => $controller . 'edit')); // ->before('not.self');
    Route::delete('{id}', array('as' => $resource . '.destroy', 'uses' => $controller . 'destroy')); // ->before('not.self');
    Route::any('create-lottery/{id}', array('as' => $resource . '.create-lottery', 'uses' => $controller . 'createLotteryFromSeries')); // ->before('not.self');
});