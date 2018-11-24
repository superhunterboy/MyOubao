<?php

# User管理
Route::group(['prefix' => 'mobile-lotteries'], function () {
    $resource = 'mobile-lotteries';
    $controller = 'MobileLotteryController@';
    Route::any('/bet/{lottery_id?}', ['as' => $resource . '.bet', 'uses' => $controller . 'bet']);
    Route::any('/user-lottery-info', ['as' => $resource . '.user-lottery-info', 'uses' => $controller . 'getLotteryInfos']);
    Route::any('/load-data/{step}/{lottery_id?}', ['as' => $resource . '.load-data', 'uses' => $controller . 'loadData']);
    Route::any('/load-issues/{lottery_id?}', ['as' => $resource . '.load-issues', 'uses' => $controller . 'getIssues']);
    Route::any('/load-lottery-prize-set/{lottery_id}', ['as' => $resource . '.load-lottery-prize-set', 'uses' => $controller . 'getPrizeDetailsByLottery']);
});
