<?php
/**
 * AccountSnapshot管理
 */
$sUrlDir = 'account-snapshots';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'account-snapshots';
    $controller = 'AccountSnapshotController@';
    Route::get('/', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::get('/download', array('as' => $resource . '.download', 'uses' => $controller . 'download'));
});
