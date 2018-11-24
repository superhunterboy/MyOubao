<?php

    /**
 * Way管理
 */
$sUrlDir = 'activity-user-logs';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'activity-user-logs';
    $controller = 'ActivityUserLogController@';
    Route::get('/', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
});
