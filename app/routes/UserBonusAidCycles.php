<?php

/**
 * 代理分红红利
 */
$sUrlDir = 'user-bonus-aid-cycles';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'user-bonus-aid-cycles';
    $controller = 'UserBonusAidCycleController@';
    Route::any( '{user_id}/index', ['as' => $resource . '.index' ,  'uses' => $controller . 'index'  ]);
});