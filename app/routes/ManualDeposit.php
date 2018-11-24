<?php

/**
 * 手动充值管理
 */
Route::group(['prefix' => 'manual-deposits'], function () {
    $resource = 'manual-deposits';
    $controller = 'ManualDepositController@';
    Route::get('/', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::any('batch-deposit', array('as' => $resource . '.batch-deposit', 'uses' => $controller . 'batchDeposit'));
    Route::any('verify/{id}', array('as' => $resource . '.verify', 'uses' => $controller . 'verify'));
    Route::any('refuse/{id}', array('as' => $resource . '.refuse', 'uses' => $controller . 'refuse2'));
    Route::any('batch-verify', array('as' => $resource . '.batch-verify', 'uses' => $controller . 'batchVerify'));
    Route::get('/download', array('as' => $resource . '.download', 'uses' => $controller . 'download'));
});
