<?php

$sUrlDir = 'jc-verified-matches';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'jc-verified-matches';
    $controller = 'JcController\VerifiedMatchesController@';
    Route::get('/index', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::any('/verify-score', array('as' => $resource . '.verifyScore', 'uses' => $controller . 'verifyScore'));
    Route::any('/batch-verify-score', array('as' => $resource . '.batchVerifyScore', 'uses' => $controller . 'batchVerifyScore'));
//    Route::get('/methodControll', array('as' => $resource . '.methodControll', 'uses' => $controller . 'methodControll'));
    Route::any(       '{id}/edit', ['as' => $resource . '.edit' ,   'uses' => $controller . 'edit'   ]); // be disabled
    Route::any(       '/create', ['as' => $resource . '.create' ,   'uses' => $controller . 'create'   ]); // be disabled
    Route::delete(         '{id}', ['as' => $resource . '.destroy', 'uses' => $controller . 'destroy']);
});
