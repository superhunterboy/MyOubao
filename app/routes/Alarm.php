<?php

// 提醒
Route::group(['prefix' => 'alarm'], function () {
    $resource = 'alarm';
    $controller = 'AlarmController@';
    Route::get('withdraw', ['as' => $resource . '.withdraw', 'uses' => $controller . 'checkWithdrawNewFlag']);
    Route::get('withdraw-finance', ['as' => $resource . '.withdraw-finance', 'uses' => $controller . 'checkWithdrawNewFlagForFinance']);
    Route::get('deposit', ['as' => $resource . '.withdraw', 'uses' => $controller . 'checkDepositNewFlag']);
});