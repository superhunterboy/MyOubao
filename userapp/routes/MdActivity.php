<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


# 中秋活动路由
Route::group(['prefix' => 'mdactivity'], function () {
    $resource = 'mdactivity';
    $controller = 'MdActivityController@';
    Route::get( '/', ['as' => $resource . '.index', 'uses' => $controller . 'index']);
    Route::post( 'reward', ['as' => $resource . '.reward', 'uses' => $controller . 'isReward']);
//    Route::any( 'create', ['as' => $resource . '.create', 'uses' => $controller . 'create']);
    Route::get('history', ['as' => $resource . '.history', 'uses' => $controller . 'historyReward']);
//    Route::any('{id}/edit', ['as' => $resource . '.edit', 'uses' => $controller . 'edit']);
//    Route::delete( '{id}', ['as' => $resource . '.destroy', 'uses' => $controller . 'destroy']);
});