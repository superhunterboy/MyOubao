<?php

/**
 * MissedTask
 */
$sUrlDir = 'missed-tasks';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'missed-tasks';
    $controller = 'MissedTaskController@';
    Route::get( '/', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    // Route::any( 'create', array('as' => $resource . '.create', 'uses' => $controller . 'create'));
     Route::get('{id}/view', array('as' => $resource . '.view' , 'uses' => $controller . 'view'   ));
});