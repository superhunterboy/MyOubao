<?php
/**
 * 管理员日志管理
 */
$sUrlDir = 'admin-logs';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'admin-logs';
    $controller = 'AdminLogController@';
    Route::get(        '/', ['as' => $resource . '.index',       'uses' => $controller . 'index']);
    Route::any(        '{id}/view', ['as' => $resource . '.view',        'uses' => $controller . 'view']);
});

