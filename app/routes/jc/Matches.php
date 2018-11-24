<?php

$sUrlDir = 'jc-matches';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'jc-matches';
    $controller = 'JcController\MatchesController@';
    Route::get('/index', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::any('create/{id?}', array('as' => $resource . '.create', 'uses' => $controller . 'create'));
    Route::any('matches-verify', array('as' => $resource . '.matchesVerify', 'uses' => $controller . 'matchesVerify'));
    Route::any('batch-verify', array('as' => $resource . '.batchVerify', 'uses' => $controller . 'batchVerify'));
    Route::get('{id}/view', array('as' => $resource . '.view', 'uses' => $controller . 'view'));
    Route::any('{id}/edit', array('as' => $resource . '.edit', 'uses' => $controller . 'edit')); // ->before('not.self');
    Route::delete('{id}', array('as' => $resource . '.destroy', 'uses' => $controller . 'destroy')); // ->before('not.self');
});
