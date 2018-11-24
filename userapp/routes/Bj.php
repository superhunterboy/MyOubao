<?php

# 投注
Route::group(['prefix' => 'casino'], function () {

    $resource = 'casino';
    $controller = 'CasinoBetController@';

    Route::any(      '/bet/{lottery_id?}/{table_id?}', ['as' => $resource . '.bet','uses'       => $controller . 'bet']);


});
