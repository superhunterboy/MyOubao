<?php

/**
 *  活动报表
 */
Route::group(['prefix' => 'activity-report-cash-vouchers'], function () {
    $resource = 'activity-report-cash-vouchers';
    $controller = 'ActivityReportCashVoucherController@';
    Route::get('/', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::get('/download', array('as' => $resource . '.download', 'uses' => $controller . 'download'));
});


Route::group(['prefix' => 'activity-report-deposit-onces'], function () {
    $resource = 'activity-report-deposit-onces';
    $controller = 'ActivityReportDepositOnceController@';
    Route::get('/', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::get('/download', array('as' => $resource . '.download', 'uses' => $controller . 'download'));
});

Route::group(['prefix' => 'activity-report-deposit4-times'], function () {
    $resource = 'activity-report-deposit4-times';
    $controller = 'ActivityReportDeposit4TimesController@';
    Route::get('/', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::get('/download', array('as' => $resource . '.download', 'uses' => $controller . 'download'));
});

Route::group(['prefix' => 'activity-report-player-stats'], function () {
    $resource = 'activity-report-player-stats';
    $controller = 'ActivityReportPlayerStatController@';
    Route::get('/', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::get('/download', array('as' => $resource . '.download', 'uses' => $controller . 'download'));
});

Route::group(['prefix' => 'activity-report-real-object-prizes'], function () {
    $resource = 'activity-report-real-object-prizes';
    $controller = 'ActivityReportRealObjectPrizeController@';
    Route::get('/', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::get('/download', array('as' => $resource . '.download', 'uses' => $controller . 'download'));
});

Route::group(['prefix' => 'activity-report-rebate-vouchers'], function () {
    $resource = 'activity-report-rebate-vouchers';
    $controller = 'ActivityReportRebateVoucherController@';
    Route::get('/', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::get('/download', array('as' => $resource . '.download', 'uses' => $controller . 'download'));
});

Route::group(['prefix' => 'activity-report-register10s'], function () {
    $resource = 'activity-report-register10s';
    $controller = 'ActivityReportRegister10Controller@';
    Route::get('/', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::get('/download', array('as' => $resource . '.download', 'uses' => $controller . 'download'));
});

Route::group(['prefix' => 'activity-report-withdrawals'], function () {
    $resource = 'activity-report-withdrawals';
    $controller = 'ActivityReportWithdrawalController@';
    Route::get('/', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::get('/download', array('as' => $resource . '.download', 'uses' => $controller . 'download'));
});
