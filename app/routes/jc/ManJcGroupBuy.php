<?php

$sUrlDir = 'jc-group-buys';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'jc-group-buys';
    $controller = 'JcController\GroupBuyController@';
    Route::get('/', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
//    Route::any('create/{id?}', array('as' => $resource . '.create', 'uses' => $controller . 'create'));
    Route::any('view/{id?}', array('as' => $resource . '.view', 'uses' => $controller . 'view'));
    Route::get('{id}/view-bet-detail', array('as' => $resource . '.view-bet-detail', 'uses' => $controller . 'viewBetDetail'));
    Route::get('{id}/bet-detail', array('as' => $resource . '.bet-detail', 'uses' => $controller . 'betDetail'));
    Route::post('set-order', array('as' => $resource . '.set-order', 'uses' => $controller . 'setOrder'));
    Route::any('drop/{id}', array('as' => $resource . '.drop', 'uses' => $controller . 'drop'));
//    Route::get('{id}/bet-detail', array('as' => $resource . '.bet-detail', 'uses' => $controller . 'betDetail'));
//    Route::get('{id}/cancel-bet', array('as' => $resource . '.cancel-bet', 'uses' => $controller . 'cancelBet'));
//    Route::any('{id}/edit', array('as' => $resource . '.edit', 'uses' => $controller . 'edit')); // ->before('not.self');
//    Route::delete('{id}', array('as' => $resource . '.destroy', 'uses' => $controller . 'destroy')); // ->before('not.self');
});
