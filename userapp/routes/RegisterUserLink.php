<?php

# 开户链接管理, 只有总代/代理可以访问
Route::group(['prefix' => 'user-links', 'before' => 'agent'], function () {
    $resource = 'user-links';
    $controller = 'UserRegisterLinkController@';
    Route::get(        '/', ['as' => $resource . '.index',   'uses' => $controller . 'index']);
    Route::any(   'create', ['as' => $resource . '.create',  'uses' => $controller . 'create']);
    Route::get('{id}/view', ['as' => $resource . '.view',    'uses' => $controller . 'view']);
    Route::get(     '{id}', ['as' => $resource . '.destroy', 'uses' => $controller . 'closeLink']);

});