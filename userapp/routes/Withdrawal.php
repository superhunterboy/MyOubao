<?php

# 提现管理
Route::group(['prefix' => 'withdrawals'], function () {
    $resource = 'user-withdrawals';
    $controller = 'UserWithdrawalController@';
    Route::get( '/', ['as' => $resource . '.index', 'uses' => $controller . 'index']);
    Route::any('withdraw/{step?}', ['as' => $resource . '.withdraw', 'uses' => $controller . 'withdraw']);
});