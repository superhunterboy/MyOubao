<?php

/**
 * 异常充值管理
 */
Route::group(['prefix' => 'exception-deposits'], function () {
    $resource = 'exception-deposits';
    $controller = 'ExceptionDepositController@';
    Route::get( '/', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::get('{id}/view', array('as' => $resource . '.view' , 'uses' => $controller . 'view'   ));
    
    Route::put('{id}/add-coin', array('as' => $resource . '.add-coin' , 'uses' => $controller . 'addCoin'   ));
    Route::get('/edit', array('as' => $resource . '.edit' , 'uses' => $controller . 'edit'   ));
    Route::put('{id}/ignore', array('as' => $resource . '.ignore' , 'uses' => $controller . 'ignore'   ));
    Route::any('{id}/refund/', array('as' => $resource . '.refund' , 'uses' => $controller . 'refund'   ));
    Route::any('{id}/process', array('as' => $resource . '.process' , 'uses' => $controller . 'process'   ));
    Route::any('{id}/cancel-process', array('as' => $resource . '.cancelProcess' , 'uses' => $controller . 'cancelProcess'   ));
    Route::get('/download', array('as' => $resource . '.download', 'uses' => $controller . 'download'));

});
