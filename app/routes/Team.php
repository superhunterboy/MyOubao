<?php

$sUrlDir = 'jc-teams';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'jc-teams';
    $controller = 'JcController\TeamController@';
    Route::get('/index', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
});
