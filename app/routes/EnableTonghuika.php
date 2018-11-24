<?php


Route::group(['prefix' => 'enable-tonghuikas'], function () {
    Route::any('/', array('as' => 'enable-tong-hui-kas.index', 'uses' => 'EnableTongHuiKaController@index'));
});
