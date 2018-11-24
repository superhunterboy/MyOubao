<?php

$sUrlDir = 'jc-user-growths';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'jc-user-growths';
    $controller = 'JcController\UserGrowthController@';
    Route::get('/index', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::any(       '{id}/edit', ['as' => $resource . '.edit' ,   'uses' => $controller . 'edit'   ]); // be disabled
    Route::any(       '/create', ['as' => $resource . '.create' ,   'uses' => $controller . 'create'   ]); // be disabled
//    Route::delete(         '{id}', ['as' => $resource . '.destroy', 'uses' => $controller . 'destroy']);
});
