<?php

# 盈亏报表
Route::group(['prefix' => 'activity'], function () {
    $resource = 'activity';
    $controller = 'UserActivityController@';
    Route::get(         '/', ['as' => $resource . '.index',      'uses' => $controller . 'index']);
    Route::get(         '/dailybet', ['as' => $resource . '.dailybet',      'uses' => $controller . 'dailyBet']);
    Route::get(         '/dailybetreward/{reward?}', ['as' => $resource . '.dailybetreward',      'uses' => $controller . 'getDailyBetReward']);
    Route::get(         '/dailysignin', ['as' => $resource . '.dailysignin',      'uses' => $controller . 'dailySignin']);
    Route::get(         '/newcharge', ['as' => $resource . '.newcharge',      'uses' => $controller . 'newCharge']);
    Route::get(         '/newchargereward/{reward?}', ['as' => $resource . '.newchargereward',      'uses' => $controller . 'getNewDepositReward']);
    Route::get(         '/dailycharge', ['as' => $resource . '.dailycharge',      'uses' => $controller . 'dailyCharge']);
    Route::get(         '/dailychargereward/{reward?}', ['as' => $resource . '.dailychargereward',      'uses' => $controller . 'getDailyDepositReward']);
    Route::any(         '/punchin/{day?}', ['as' => $resource . '.punchin',      'uses' => $controller . 'punchIn']);
});