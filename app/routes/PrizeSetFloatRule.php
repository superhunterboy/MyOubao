<?php

/**
 * PrizeSetFloatRule管理
 */
$sUrlDir = 'prize-set-float-rules';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'prize-set-float-rules';
    $controller = 'PrizeSetFloatRuleController@';
    Route::get('/index', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::any('/update', array('as' => $resource . '.update', 'uses' => $controller . 'updateRule'));
    Route::delete('/delete', array('as' => $resource . '.delete', 'uses' => $controller . 'delete')); // ->before('not.self');
});
