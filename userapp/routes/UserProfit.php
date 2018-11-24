<?php

# 盈亏报表
Route::group(['prefix' => 'user-profits'], function () {
    $resource = 'user-profits';
    $controller = 'UserUserProfitController@';
    Route::get(         '/', ['as' => $resource . '.index',      'uses' => $controller . 'index']);
    Route::get(         'withdraw-deposit', ['as' => $resource . '.withdraw-deposit',      'uses' => $controller . 'withdraw_deposit']);
    Route::get('commission', ['as' => $resource . '.commission', 'uses' => $controller . 'commission']);
    Route::get('bonus', ['as' => $resource . '.bonus', 'uses' => $controller . 'bonus']);
});