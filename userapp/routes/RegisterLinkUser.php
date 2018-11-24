<?php

# 开户链接已绑定用户管理
Route::group(['prefix' => 'user-link-users'], function () {
    $resource = 'user-link-users';
    $controller = 'UserRegisterLinkUserController@';
    Route::get(        '/', ['as' => $resource . '.index',   'uses' => $controller . 'index']);
    // Route::any(   'create', ['as' => $resource . '.create',  'uses' => $controller . 'create']);
    // Route::get('{id}/view', ['as' => $resource . '.view',    'uses' => $controller . 'view']);
    // Route::delete(  '{id}', ['as' => $resource . '.destroy', 'uses' => $controller . 'destroy']);

});