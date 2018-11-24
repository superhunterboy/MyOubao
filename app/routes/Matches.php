<?php

$sUrlDir = 'matches';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'matches';
    $controller = 'MatchesController@';
    Route::get('/index', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::any('create/{id?}', array('as' => $resource . '.create', 'uses' => $controller . 'create'));
    Route::any('matchesVerify', array('as' => $resource . '.matchesVerify', 'uses' => $controller . 'matchesVerify'));
    Route::any('betchVerify', array('as' => $resource . '.betchVerify', 'uses' => $controller . 'betchVerify'));
    Route::get('{id}/view', array('as' => $resource . '.view', 'uses' => $controller . 'view'));
    Route::any('{id}/edit', array('as' => $resource . '.edit', 'uses' => $controller . 'edit')); // ->before('not.self');
    Route::delete('{id}', array('as' => $resource . '.destroy', 'uses' => $controller . 'destroy')); // ->before('not.self');
});
