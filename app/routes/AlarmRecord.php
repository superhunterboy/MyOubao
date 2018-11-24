<?php

/**
 * EC alarm记录管理
 */
$sUrlDir = 'alarm-records';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'alarm-records';
    $controller = 'AlarmRecordController@';
    Route::get('/index', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::any('create/{id?}', array('as' => $resource . '.create', 'uses' => $controller . 'create'));
    Route::get('{id}/cancelcode', array('as' => $resource . '.cancelcode', 'uses' => $controller . 'cancelCode'));
    Route::get('{id}/revisecode', array('as' => $resource . '.revisecode', 'uses' => $controller . 'reviseCode'));
    Route::get('{id}/advancecode', array('as' => $resource . '.advancecode', 'uses' => $controller . 'advanceCode'));
    Route::get('{id}/view', array('as' => $resource . '.view', 'uses' => $controller . 'view'));
    Route::any('{id}/edit', array('as' => $resource . '.edit', 'uses' => $controller . 'edit')); // ->before('not.self');
    Route::delete('{id}', array('as' => $resource . '.destroy', 'uses' => $controller . 'destroy')); // ->before('not.self');
});
