<?php

# 体现管理
Route::group(['prefix' => 'mobile-withdrawals'], function () {
    $resource = 'mobile-withdrawals';
    $controller = 'MobileWithdrawalController@';
    Route::any( '/', ['as' => $resource . '.index', 'uses' => $controller . 'index']);
    Route::any('withdraw/{step?}', ['as' => $resource . '.withdraw', 'uses' => $controller . 'withdraw']);
});