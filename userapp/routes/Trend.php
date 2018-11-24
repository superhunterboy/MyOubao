<?php

# 开奖走势管理
Route::group(['prefix' => 'user-trends'],function (){
    $resource   = 'user-trends';
    $controller = 'UserTrendController@';
    Route::get('trend-view/{lottery_id?}/{num_type?}', ['as' => $resource . '.trend-view',  'uses' => $controller . 'trendView']);
    Route::match(['GET', 'POST'],        'trend-data', ['as' => $resource . '.trend-data',  'uses' => $controller . 'getTrendData']); //->before('ajax');

});