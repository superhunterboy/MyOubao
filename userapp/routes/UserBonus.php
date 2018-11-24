<?php

# 盈亏报表
Route::group(['prefix' => 'user-bonuses'], function () {
    $resource = 'user-bonuses';
    $controller = 'UserBonusController@';
    Route::get(         '/', ['as' => $resource . '.index',      'uses' => $controller . 'index']);
});