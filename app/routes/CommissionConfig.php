<?php

/**
 * SeriesWay管理
 */
$sUrlDir = 'commission-configs';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'commission-configs';
    $controller = 'CommissionConfigController@';
    Route::get( '/', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::any('create/{id?}',array('as' => $resource . '.create','uses' => $controller . 'create'));
    Route::get('{id}/view', array('as' => $resource . '.view' , 'uses' => $controller . 'view'   ));
    Route::any('{id}/edit', array('as' => $resource . '.edit', 'uses' => $controller . 'edit'));
    Route::delete('{id}', array('as' => $resource . '.destroy', 'uses' => $controller . 'destroy'));
});
