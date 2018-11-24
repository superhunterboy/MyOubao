<?php

    /**
 * Way管理ActiveRedEnvelopeUserController
 */
$sUrlDir = 'active-red-envelope-users';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'active-red-envelope-users';
    $controller = 'ActiveRedEnvelopeUserController@';
    Route::get('/index', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::get('{id}/view', array('as' => $resource . '.view' , 'uses' => $controller . 'view'   ));
});
