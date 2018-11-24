<?php

Route::group([], function () {
    $controller = 'CashBonusController@';
    $resource = 'cash-bonuses';
    //Route::get( '/cash-bonuses/index',   ['as' => $resource.'.index',   'uses' => $controller.'index']);
    Route::get( '/cash-bonuses/set',   ['as' => $resource.'.index',   'uses' => $controller.'index']);
    Route::get('/cash-bonuses/view/{id}',   ['as' => $resource.'.view',    'uses' => $controller.'view']);
    Route::any('/cash-bonuses/auditing/{id}',   ['as' => $resource.'.auditing',    'uses' => $controller.'auditing']);
    //Route::get('/cash-bonuses/create/{id}',   ['as' => $resource.'.create',    'uses' => $controller.'create']);


});

Route::group([], function () {
    $controller = 'CashBonusSendController@';
    $resource = 'cash-bonus-sends';
    Route::get('cash-bonus-sends/index',     ['as' => $resource.'.index',    'uses' => $controller.'index']);
    Route::any('/cash-bonus-sends/auditing/{id}',   ['as' => $resource.'.auditing',    'uses' => $controller.'auditing']);
    Route::get('cash-bonus-sends/auditing_all',      ['as'=> $resource.'.auditing_all',    'uses' => $controller.'auditing_all']);
    Route::get('/cash-bonus-sends/view/{id}',   ['as' => $resource.'.view',    'uses' => $controller.'view']);

});

Route::group([], function () {
    $controller = 'CashBonusDepositController@';
    $resource = 'cash-bonus-deposits';
    Route::get('/cash-bonus-deposits/index',     ['as' => $resource.'.index',    'uses' => $controller.'index']);
    Route::any('/cash-bonus-deposits/auditing/{id}',   ['as' => $resource.'.auditing',    'uses' => $controller.'auditing']);
    Route::get('/cash-bonus-deposits/view/{id}',   ['as' => $resource.'.view',    'uses' => $controller.'view']);
});