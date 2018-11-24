<?php

# 盈亏报表
Route::group(['prefix' => 'team-profits'], function () {
    $resource = 'team-profits';
    $controller = 'UserTeamProfitController@';
    Route::get(         '/', ['as' => $resource . '.index',      'uses' => $controller . 'index']);
    Route::get(         '/home', ['as' => $resource . '.home',      'uses' => $controller . 'home']);
    Route::get(         'group', ['as' => $resource . '.group',      'uses' => $controller . 'groupIndex']);
    Route::get('commission', ['as' => $resource . '.commission', 'uses' => $controller . 'commission']);
    Route::get('self', ['as' => $resource . '.self', 'uses' => $controller . 'self']);
});