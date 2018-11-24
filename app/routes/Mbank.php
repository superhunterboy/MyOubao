<?php

/**
 * Bank管理
 */
$sUrlDir = 'mbanks';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'mbanks';
    $controller = 'MbankController@';
 
    Route::get( '/', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::any( '{bank_id}/create', array('as' => $resource . '.create', 'uses' => $controller . 'create'));
    Route::get('{id}/view', array('as' => $resource . '.view' , 'uses' => $controller . 'view'   ));
    Route::any('{id}/edit', array('as' => $resource . '.edit', 'uses' => $controller . 'edit')); // ->before('not.self');
    Route::delete('{id}', array('as' => $resource . '.destroy', 'uses' => $controller . 'destroy')); // ->before('not.self');
    Route::any( '{id}/edit-mbank', array('as' => $resource . '.edit-mbank', 'uses' => $controller . 'editMbank'));
//    Route::get('{id}/fee/view', array('as' => $resource . '.fee_view' , 'uses' => $controller . 'feeView'   ));
//    array('as' => $resource . '.edit-mbank', 'uses' => $controller . 'editMbank')
//    Route::any('{id}/fee/edit', array('as' => $resource . '.fee_edit', 'uses' => $controller . 'feeEdit'));
//    Route::get('prohibitedWithdraw', array('as' => $resource . '.prohibitedWithdraw' , 'uses' => $controller . 'prohibitedWithdraw'   ));
//    Route::get('withdrawal-channel', array('as' => $resource . '.withdrawalChannel' , 'uses' => $controller . 'withdrawalChannel'   ));
});
