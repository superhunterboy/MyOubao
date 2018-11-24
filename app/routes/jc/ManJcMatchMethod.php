<?php

$sUrlDir = 'match-methods';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'match-methods';
    $controller = 'JcController\MatchMethodController@';
    Route::any('/method-control', array('as' => $resource . '.methodControl', 'uses' => $controller . 'methodControl'));
});
