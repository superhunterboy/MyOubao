<?php

/**
 * 行政区划管理
 */
$sUrlDir = 'districts';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'districts';
    $controller = 'DistrictController@';
    Route::get( '/', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::any( 'create/{parent_id?}', array('as' => $resource . '.create', 'uses' => $controller . 'create'));
    Route::get('{id}/view', array('as' => $resource . '.view' , 'uses' => $controller . 'view'   ));
    Route::any('{id}/edit', array('as' => $resource . '.edit', 'uses' => $controller . 'edit'));
    Route::delete('{id}', array('as' => $resource . '.destroy', 'uses' => $controller . 'destroy'));
    Route::any('generate', array('as' => $resource . '.generate', 'uses' => $controller . 'generateWidget'));
    Route::post('set-order', array('as' => $resource . '.set-order', 'uses' => $controller . 'setOrder'));
});
