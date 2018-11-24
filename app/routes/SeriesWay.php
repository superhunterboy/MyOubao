<?php

/**
 * SeriesWay管理
 */
$sUrlDir = 'series-ways';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'series-ways';
    $controller = 'SeriesWayController@';
    Route::get( '/', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::any('create/{id?}',array('as' => $resource . '.create','uses' => $controller . 'create'));
    Route::get('{id}/view', array('as' => $resource . '.view' , 'uses' => $controller . 'view'   ));
    Route::any('{id}/edit', array('as' => $resource . '.edit', 'uses' => $controller . 'edit'));
    Route::delete('{id}', array('as' => $resource . '.destroy', 'uses' => $controller . 'destroy'));
    Route::get('make-data/{id?}',array('as' => $resource . '.make-data','uses' => $controller . 'initData'));
    Route::get('test-get-wnumber/{series_id?}', array('as' => $resource . '.test-get-wnumber', 'uses' => $controller . 'testGetWnNumber')); 
    Route::any('set-note/{id?}', array('as' => $resource . '.set-note', 'uses' => $controller . 'setNote'));
});
