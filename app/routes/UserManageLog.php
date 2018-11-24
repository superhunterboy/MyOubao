<?php

/**
 * UserManageLog管理
 */
$sUrlDir = 'user-manage-logs';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'user-manage-logs';
    $controller = 'UserManageLogController@';
    Route::get(       '/', ['as' => $resource . '.index',   'uses' => $controller . 'index']);
    // Route::any('create/{id?}', ['as' => $resource . '.create',  'uses' => $controller . 'create']);
    // Route::get(   '{id}/view', ['as' => $resource . '.view',    'uses' => $controller . 'view']);
    // Route::any(   '{id}/edit', ['as' => $resource . '.edit',    'uses' => $controller . 'edit']);
    Route::delete(         '{id}', ['as' => $resource . '.destroy', 'uses' => $controller . 'destroy']);
    Route::post('update-comments', ['as' => $resource . '.update-comments', 'uses' => $controller . 'updateComments']);
});
