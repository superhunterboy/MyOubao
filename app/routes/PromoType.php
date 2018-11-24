<?php

/**
 * 活动类型管理
 */
$sUrlDir = 'promo-types';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'promo-types';
    $controller = 'PromoTypeController@';
    Route::get(      '/index', ['as' => $resource . '.index',   'uses' => $controller . 'index']);
    Route::any('create/{id?}', ['as' => $resource . '.create',  'uses' => $controller . 'create']);
    Route::get(   '{id}/view', ['as' => $resource . '.view',    'uses' => $controller . 'view']);
    Route::any(   '{id}/edit', ['as' => $resource . '.edit',    'uses' => $controller . 'edit']);
    Route::delete(     '{id}', ['as' => $resource . '.destroy', 'uses' => $controller . 'destroy']);
});
