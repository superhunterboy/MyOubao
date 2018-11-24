<?php

/**
 * Account管理
 */
$sUrlDir = 'user-extra-infos';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'user-extra-infos';
    $controller = 'UserExtraInfoController@';
    Route::get(      '/index', ['as' => $resource . '.index',   'uses' => $controller . 'index']);
});
