<?php

/**
 * 链接开户的用户列表
 */
$sUrlDir = 'register-link-users';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'register-link-users';
    $controller = 'RegisterLinkUserController@';
    Route::get(        '/', ['as' => $resource . '.index',  'uses' => $controller . 'index']);
    Route::get('{id}/view', ['as' => $resource . '.view',   'uses' => $controller . 'view']);
});
