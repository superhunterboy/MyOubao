<?php

    /**
 * Way管理
 */
$sUrlDir = 'active-red-envelopes';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'active-red-envelopes';
    $controller = 'ActiveRedEnvelopeController@';
    Route::get('/index', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::any('set-ways',array('as' => $resource . '.set-ways','uses' => $controller . 'setWays'));
    Route::any('generate', array('as' => $resource . '.generate' , 'uses' => $controller . 'generate'   ));
    Route::any('batch-delete', array('as' => $resource . '.batch-delete' , 'uses' => $controller . 'batchDelete'   ));
    Route::any('config-settings', array('as' => $resource . '.config-settings' , 'uses' => $controller . 'configSettings'   ));
        
});
