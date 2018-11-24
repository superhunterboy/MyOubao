<?php

# 注单管理
Route::group(['prefix' => 'mobile-projects'], function () {
    $resource = 'mobile-projects';
    $controller = 'MobileProjectController@';
    Route::any(            '/', ['as' => $resource . '.index',       'uses' => $controller . 'index']);
//    Route::get( '/mini-window', ['as' => $resource . '.mini-window', 'uses' => $controller . 'miniWindow']);

    // Route::any( 'create', ['as' => $resource . '.create', 'uses' => $controller . 'create']);
    Route::any(    '{id}/view', ['as' => $resource . '.view', 'uses' => $controller . 'view']);
    // Route::any('{id}/edit', ['as' => $resource . '.edit', 'uses' => $controller . 'edit']);
    Route::any(    '{id}/drop', ['as' => $resource . '.drop', 'uses' => $controller . 'drop']);
});
