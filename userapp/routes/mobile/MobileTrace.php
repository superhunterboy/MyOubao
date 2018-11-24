<?php

# 追号管理
Route::group(['prefix' => 'mobile-traces'],function (){
    $resource   = 'mobile-traces';
    $controller = 'MobileTraceController@';
    Route::any(                 '/', ['as' => $resource . '.index',  'uses' => $controller . 'index']);
    Route::any(        '{id?}/view', ['as' => $resource . '.view',   'uses' => $controller . 'view']);
//    Route::any(         '{id}/drop', ['as' => $resource . '.stop',   'uses' => $controller . 'drop']);
    Route::any('{id}/cancel',['as' => $resource . '.cancel','uses' => $controller . 'cancel']);
});
