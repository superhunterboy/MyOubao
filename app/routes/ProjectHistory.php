<?php

/**
 * Transaction管理
 */
$sUrlDir = 'project-histories';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'project-histories';
    $controller = 'ProjectHistoryController@';
    Route::get('/', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::any('{id}/view', ['as' => $resource . '.view' , 'uses' => $controller . 'view'   ]);
    Route::get(           '/download', ['as' => $resource . '.download',   'uses' => $controller . 'download']);
    Route::get('{id}/check-bet-num',['as'=>$resource.'.check-bet-num','uses'=>$controller.'checkBetNum']);
});
