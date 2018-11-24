<?php

$sUrlDir = 'jc-projects';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'jc-projects';
    $controller = 'JcController\ProjectController@';
    Route::get('/', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
//    Route::any('create/{id?}', array('as' => $resource . '.create', 'uses' => $controller . 'create'));
    Route::get('{id}/view', array('as' => $resource . '.view', 'uses' => $controller . 'view'));
    Route::get('{id}/viewBet', array('as' => $resource . '.viewBet', 'uses' => $controller . 'viewBet'));
    Route::get('{id}/viewGroupBuy', array('as' => $resource . '.viewGroupBuy', 'uses' => $controller . 'viewGroupBuy'));
    Route::get(           '/download', ['as' => $resource . '.download',   'uses' => $controller . 'download']);
    Route::get('{id}/drop', array('as' => $resource . '.drop', 'uses' => $controller . 'drop'));
//    Route::get('{id}/bet-detail', array('as' => $resource . '.bet-detail', 'uses' => $controller . 'betDetail'));
//    Route::get('{id}/cancel-bet', array('as' => $resource . '.cancel-bet', 'uses' => $controller . 'cancelBet'));
//    Route::any('{id}/edit', array('as' => $resource . '.edit', 'uses' => $controller . 'edit')); // ->before('not.self');
//    Route::delete('{id}', array('as' => $resource . '.destroy', 'uses' => $controller . 'destroy')); // ->before('not.self');
});
