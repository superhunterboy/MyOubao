<?php

/**
 * Message Content管理
 */
$sUrlDir = 'msg-messages';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'msg-messages';
    $controller = 'MsgMessageController@';
    Route::get(    '/', ['as' => $resource . '.index',   'uses' => $controller . 'index']);
    Route::any(       'create/{receiver_type?}', ['as' => $resource . '.create',  'uses' => $controller . 'createMessage']);
    Route::get(    '{id}/view', ['as' => $resource . '.view' ,   'uses' => $controller . 'view'   ]);
    Route::any(    '{id}/edit', ['as' => $resource . '.edit',    'uses' => $controller . 'edit']);
    Route::delete(      '{id}', ['as' => $resource . '.destroy', 'uses' => $controller . 'destroy']);
    Route::any( '{id}/deliver', ['as' => $resource . '.deliver', 'uses' => $controller . 'deliverMessage']);
});