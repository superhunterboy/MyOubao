<?php

# 盈亏报表
Route::group(['prefix' => 'reports'], function () {
    $resource = 'reports';
    $controller = 'ReportController@';
    Route::get(      '/', ['as' => $resource . '.index', 'uses' => $controller . 'index']);
    Route::get('{id}/agentDividend', ['as' => $resource . '.agentDividend', 'uses' => $controller . 'agentDividend']);
    Route::get( '{id}/agentLoss', ['as' => $resource . '.agentLoss', 'uses' => $controller . 'agentLoss']);
    Route::get( '{id}/agentRebate', ['as' => $resource . '.agentRebate', 'uses' => $controller . 'agentRebate']);
});