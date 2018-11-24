<?php

/**
 * Way管理
 */
$sUrlDir = 'activity-user-conditions';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'activity-user-conditions';
    $controller = 'ActivityUserConditionController@';
    Route::get('/index', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::any('create/{id}', array('as' => $resource . '.create', 'uses' => $controller . 'create'));
    Route::get('{id}/view', array('as' => $resource . '.view', 'uses' => $controller . 'view'));
    Route::any('{id}/edit', array('as' => $resource . '.edit', 'uses' => $controller . 'edit')); // ->before('not.self');
    Route::delete('{id}', array('as' => $resource . '.destroy', 'uses' => $controller . 'destroy')); // ->before('not.self');
});
