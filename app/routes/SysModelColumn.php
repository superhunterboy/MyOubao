<?php

/**
 * SysModelColumn管理
 */
$sUrlDir = 'sys-model-columns';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'sys-model-columns';
    $controller = 'SysModelColumnController@';
    Route::get( '/', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
//    Route::get('update_models', array('as' => $resource . '.updateModels', 'uses' => $controller . 'updateModels')); // ->before('not.self');
    Route::any( 'create', array('as' => $resource . '.create', 'uses' => $controller . 'create'));
    Route::get('{id}/view', array('as' => $resource . '.view' , 'uses' => $controller . 'view'   ));
    Route::any('{id}/edit', array('as' => $resource . '.edit', 'uses' => $controller . 'edit')); // ->before('not.self');
    Route::delete('{id}', array('as' => $resource . '.destroy', 'uses' => $controller . 'destroy')); // ->before('not.self');
});
