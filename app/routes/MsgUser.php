<?php
    /**
 * Message User 关联关系管理
 */
$sUrlDir = 'msg-users';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'msg-users';
    $controller = 'MsgUserController@';
    Route::get(       '/', ['as' => $resource . '.index',   'uses' => $controller . 'index'  ]);
    // Route::any('create/{receiver_type?}', ['as' => $resource . '.create',  'uses' => $controller . 'createMessage' ]);
    Route::get(       '{id}/view', ['as' => $resource . '.view',    'uses' => $controller . 'viewMessage'   ]);
    Route::delete(         '{id}', ['as' => $resource . '.destroy', 'uses' => $controller . 'destroy']);
    // Route::get('/upload-receivers', ['as' => $resource . '.upload-receivers', function () {
    //     return View::make('message.uploadReceivers');
    // }]);
});