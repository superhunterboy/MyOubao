<?php

    /**
 * Way管理ActiveRedEnvelopeUserController
 */
$sUrlDir = 'active-red-envelope-rules';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'active-red-envelope-rules';
    $controller = 'ActiveRedEnvelopeRuleController@';
    Route::get('/index', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::get('{id}/view', array('as' => $resource . '.view' , 'uses' => $controller . 'view'   ));
    Route::get('{id}/set-status', array('as' => $resource . '.set-status' , 'uses' => $controller . 'setStatus'   ));
    Route::any('{id}/edit', array('as' => $resource . '.edit' , 'uses' => $controller . 'edit'   ));
    Route::any('create', array('as' => $resource . '.create' , 'uses' => $controller . 'create'   ));
});
