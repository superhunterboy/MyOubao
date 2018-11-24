<?php
/**
 * Created by PhpStorm.
 * User: wallace
 * Date: 15-9-7
 * Time: 上午11:07
 */

$sUrlDir = 'user-commissions';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'user-commissions';
    $controller = 'UserCommissionController@';
    Route::get('/', ['as' => $resource . '.index',   'uses' => $controller . 'index']);
    Route::any('verify/{id}', array('as' => $resource . '.verify', 'uses' => $controller . 'verify'));
    Route::any('refuse2/{id}', array('as' => $resource . '.refuse2', 'uses' => $controller . 'refuse2'));
    Route::any('view/{id}', array('as' => $resource . '.view', 'uses' => $controller . 'view'));
    Route::any('batch-verify', array('as' => $resource . '.batch-verify', 'uses' => $controller . 'batchVerify'));

});
