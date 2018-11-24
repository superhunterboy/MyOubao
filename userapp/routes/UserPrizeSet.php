<?php

# 投注管理
Route::group(['prefix' => 'user-user-prize-sets'], function () {
    $resource   = 'user-user-prize-sets';
    $controller = 'UserUserPrizeSetController@';
    Route   ::get(        '/', ['as' => $resource . '.index',   'uses' => $controller . 'index']);
    // Route::any(   'create', ['as' => $resource . '.create',  'uses' => $controller . 'create']);
    // Route::get('{id}/view', ['as' => $resource . '.view',    'uses' => $controller . 'view']);
    // Route::any('{id}/edit', ['as' => $resource . '.edit',    'uses' => $controller . 'edit']);
    // Route::delete(  '{id}', ['as' => $resource . '.destroy', 'uses' => $controller . 'destroy']);
    Route::get(                  'game-prize-set/{id?}', ['as' => $resource . '.game-prize-set',   'uses' => $controller . 'gamePrizeSet']);
    Route::any(      'set-prize-set/{id}/{lottery_id?}', ['as' => $resource . '.set-prize-set',    'uses' => $controller . 'setPrizeSet'])->before('agent');
    Route::get('prize-set-detail/{prize}/{lottery_id?}', ['as' => $resource . '.prize-set-detail', 'uses' => $controller . 'prizeSetDetail']);
}); 