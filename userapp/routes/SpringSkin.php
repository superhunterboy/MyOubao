<?php

# 银行卡管理
Route::group(['prefix' => 'bonus'], function () {
    $resource = 'chunjie2016';
    $controller = 'SpringSkinController@';
    Route::get(                      '/', ['as' => $resource . '.index',       'uses' => $controller . 'index']);
});