<?php

/**
 * 代理分红红利
 */
$sUrlDir = 'bonuses';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'bonuses';
    $controller = 'BonusController@';
    Route::get( '/', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    // Route::any( 'create', array('as' => $resource . '.create', 'uses' => $controller . 'create'));
     Route::get('{id}/view', array('as' => $resource . '.view' , 'uses' => $controller . 'view'   ));
     Route::any('{id}/audit-bonus', array('as' => $resource . '.audit-bonus', 'uses' => $controller . 'auditBonus')); // ->before('not.self');
     Route::any('{id}/reject-bonus', array('as' => $resource . '.reject-bonus', 'uses' => $controller . 'rejectBonus')); // ->before('not.self');
     Route::any('{id}/send-bonus', array('as' => $resource . '.send-bonus', 'uses' => $controller . 'sendBonus')); // ->before('not.self');
     Route::delete('{id}', array('as' => $resource . '.destroy', 'uses' => $controller . 'destroy')); // ->before('not.self');
});