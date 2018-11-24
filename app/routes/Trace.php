<?php
    /**
 * 追号管理
 */
$sUrlDir = 'traces';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'traces';
    $controller = 'TraceController@';
    Route::get('/', ['as' => $resource . '.index' , 'uses' => $controller . 'index'  ]);
    Route::any('{id}/view', ['as' => $resource . '.view' , 'uses' => $controller . 'view'   ]);
    Route::any('{id}/stop',['as' => $resource . '.stop','uses' => $controller . 'stop']);
    Route::any('{id}/generate',['as' => $resource . '.generate','uses' => $controller . 'setGenerateTask']);
});
