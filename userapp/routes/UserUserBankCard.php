<?php

# 银行卡管理
Route::group(['prefix' => 'user-bank-cards'], function () {
    $resource = 'bank-cards';
    $controller = 'UserUserBankCardController@';
    Route::get(                      '/', ['as' => $resource . '.index',       'uses' => $controller . 'index']);
    Route::any(       '{step}/bind-card', ['as' => $resource . '.bind-card',   'uses' => $controller . 'bindCard']);
    // Route::get('{id}/view', ['as' => $resource . '.view', 'uses' => $controller . 'view']);
//    Route::any('{step}/{id}/modify-card', ['as' => $resource . '.modify-card', 'uses' => $controller . 'modifyCard']);
//    Route::any(           '{id}/destroy', ['as' => $resource . '.destroy',     'uses' => $controller . 'customDestroy']);
    Route::any(     '{status}/card-lock', ['as' => $resource . '.card-lock',   'uses' => $controller . 'cardLock']);
     Route::any('validate/{id?}', ['as' => $resource . '.validate', 'uses' => $controller . 'validate']);
    // Route::get( 'confirm', ['as' => $resource . '.confirm', 'uses' => $controller . 'confirm']);
    // Route::get( 'result', ['as' => $resource . '.result', 'uses' => $controller . 'result']);
});

