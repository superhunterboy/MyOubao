<?php

# 平台公告
Route::group(['prefix' => 'announcements'], function () {
    $resource = 'announcements';
    $controller = 'AnnouncementController@';
    Route::get( '/', ['as' => $resource . '.index', 'uses' => $controller . 'index']);
    Route::any( 'create', ['as' => $resource . '.create', 'uses' => $controller . 'create']);
    Route::get('{id}/view', ['as' => $resource . '.view', 'uses' => $controller . 'view']);
    Route::any('{id}/edit', ['as' => $resource . '.edit', 'uses' => $controller . 'edit']);
    Route::delete( '{id}', ['as' => $resource . '.destroy', 'uses' => $controller . 'destroy']);
});
