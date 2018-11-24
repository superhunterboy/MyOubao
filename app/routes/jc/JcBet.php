<?php

$sUrlDir = 'jc-bets';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'jc-bets';
    $controller = 'JcController\BetController@';
    Route::get('/', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::any('create/{id?}', array('as' => $resource . '.create', 'uses' => $controller . 'create'));
    Route::get('{id}/view', array('as' => $resource . '.view', 'uses' => $controller . 'view'));
    Route::get('{id}/bet-detail', array('as' => $resource . '.bet-detail', 'uses' => $controller . 'betDetail'));
    Route::get('{id}/drop-bet', array('as' => $resource . '.cancel-bet', 'uses' => $controller . 'dropBet'));
    Route::any('{id}/edit', array('as' => $resource . '.edit', 'uses' => $controller . 'edit')); // ->before('not.self');
    Route::delete('{id}', array('as' => $resource . '.destroy', 'uses' => $controller . 'destroy')); // ->before('not.self');
});
