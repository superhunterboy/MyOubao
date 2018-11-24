<?php

/**
 * Message Content管理
 */
$sUrlDir = 'msg-suggestions';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'msg-suggestions';
    $controller = 'MsgSuggestionController@';
    Route::get('/', ['as' => $resource . '.index',   'uses' => $controller . 'index']);
    Route::get('open', ['as' => $resource . '.open',   'uses' => $controller . 'open']);
    Route::get('close', ['as' => $resource . '.close',   'uses' => $controller . 'close']);
});