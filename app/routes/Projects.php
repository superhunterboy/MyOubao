<?php
/**
 * 注单管理
 */
$sUrlDir = 'projects';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'projects';
    $controller = 'ProjectController@';
    Route::get('/', ['as' => $resource . '.index' , 'uses' => $controller . 'index'  ]);
    Route::any('{id}/view', ['as' => $resource . '.view' , 'uses' => $controller . 'view'   ]);
    Route::get('{id}/drop', ['as' => $resource . '.drop', 'uses' => $controller . 'drop']);
    Route::delete('{id}', ['as' => $resource . '.destroy', 'uses' => $controller . 'destroy']);
    Route::get('{id}/resend-prize',['as' => $resource . '.resend-prize','uses' => $controller . 'setPrizeTask']);
    Route::get('{id}/resend-commission',['as' => $resource . '.resend-commission','uses' => $controller . 'setCommissionTask']);
    Route::get(           '/download', ['as' => $resource . '.download',   'uses' => $controller . 'download']);
    Route::any('{id}/check-bet-num',['as'=>$resource.'.check-bet-num','uses'=>$controller.'checkBetNum']);
});
