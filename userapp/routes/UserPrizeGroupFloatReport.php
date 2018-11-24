<?php

# 盈亏报表
Route::group(['prefix' => 'user-prizeset-float-reports'], function () {
    $resource = 'user-prizeset-float-reports';
    $controller = 'UserPrizeSetFloatReportController@';
    Route::get(         '/', ['as' => $resource . '.index',      'uses' => $controller . 'index']);
});