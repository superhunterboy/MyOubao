<?php

/**
 * PassiveService管理
 */
$sUrlDir = 'overlimit-prize-groups';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'overlimit-prize-groups';
    $controller = 'OverlimitPrizeGroupController@';
    Route::get('/index', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::any('create/{top_agent_id?}', array('as' => $resource . '.create', 'uses' => $controller . 'create'));
    Route::get('{id}/view', array('as' => $resource . '.view', 'uses' => $controller . 'view'));
    Route::any('{id}/edit', array('as' => $resource . '.edit', 'uses' => $controller . 'edit')); // ->before('not.self');
    Route::delete('{id}', array('as' => $resource . '.destroy', 'uses' => $controller . 'destroy')); // ->before('not.self');
});
