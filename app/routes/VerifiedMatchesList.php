<?php

$sUrlDir = 'verified-matches-lists';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'verified-matches-lists';
    $controller = 'VerifiedMatchesListController@';
    Route::get('/index', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::any('/verifyScore', array('as' => $resource . '.verifyScore', 'uses' => $controller . 'verifyScore'));
//    Route::get('/methodControll', array('as' => $resource . '.methodControll', 'uses' => $controller . 'methodControll'));
    Route::any(       '{id}/edit', ['as' => $resource . '.edit' ,   'uses' => $controller . 'edit'   ]); // be disabled
});
