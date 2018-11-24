<?php

# 注单管理
Route::group(['prefix' => 'projects'], function () {
    $resource = 'projects';
    $controller = 'UserProjectController@';
    Route::get(            '/', ['as' => $resource . '.index',       'uses' => $controller . 'index']);
    Route::get( '/mini-window', ['as' => $resource . '.mini-window', 'uses' => $controller . 'miniWindow']);
    Route::get( '/mini-window-xy28', ['as' => $resource . '.mini-window-xy28', 'uses' => $controller . 'miniWindow4Xy28']);

    // Route::any( 'create', ['as' => $resource . '.create', 'uses' => $controller . 'create']);
    Route::get(    '/view/{id?}', ['as' => $resource . '.view', 'uses' => $controller . 'view']);
    // Route::any('{id}/edit', ['as' => $resource . '.edit', 'uses' => $controller . 'edit']);
    Route::get(    '{id}/drop/{bRedirect?}', ['as' => $resource . '.drop', 'uses' => $controller . 'drop']);
    Route::post(    '/drop-multi-projects', ['as' => $resource . '.drop-multi-projects', 'uses' => $controller . 'dropMultiProjects']);

});
