<?php

/**
 * 功能管理
 */
$sUrlDir = 'functionalities';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'functionalities';
    $controller = 'FunctionalityController@';
    Route::get('/', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    // Route::get( '{id}/sub', array('as' => $resource . '.sub' , 'uses' => $controller . 'sub'  ));
    Route::any('create/{id?}', array('as' => $resource . '.create', 'uses' => $controller . 'create'));
    Route::any('{id}/view', array('as' => $resource . '.view', 'uses' => $controller . 'view'));
    Route::any('{id}/edit', array('as' => $resource . '.edit', 'uses' => $controller . 'edit'));
    Route::delete('{id}', array('as' => $resource . '.destroy', 'uses' => $controller . 'destroy'));
    Route::get('{id}/createSubFunctionalities', array('as' => $resource . '.createSubFunctionalities', 'uses' => $controller . 'createSubFunctionalities'));
    Route::get('updateRelations', array('as' => $resource . '.updateRelations', 'uses' => $controller . 'updateRelations'));
    Route::post('set-order', array('as' => $resource . '.set-order', 'uses' => $controller . 'setOrder'));
        });
