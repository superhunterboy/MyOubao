<?php

# 平台公告
Route::group(['prefix' => 'mobile-announcements'], function () {
    $resource = 'mobile-announcements';
    $controller = 'MobileAnnouncementController@';
    Route::any('/', ['as' => $resource . '.index', 'uses' => $controller . 'index']);
    Route::any('create', ['as' => $resource . '.create', 'uses' => $controller . 'create']);
    Route::any('{id}/view', ['as' => $resource . '.view', 'uses' => $controller . 'view']);
    Route::any('{id}/edit', ['as' => $resource . '.edit', 'uses' => $controller . 'edit']);
    Route::any('{id}', ['as' => $resource . '.destroy', 'uses' => $controller . 'destroy']);
});
