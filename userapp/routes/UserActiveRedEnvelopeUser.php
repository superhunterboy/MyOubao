<?php

    /**
 * Way管理
 */
$sUrlDir = 'anniversary';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'anniversary';
    $controller = 'UserActiveRedEnvelopeUserController@';
//    Route::get('/index', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::any('get-red-envelope',array('as' => $resource . '.get-red-envelope','uses' => $controller . 'getRedEnvelope'));
});
