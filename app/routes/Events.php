<?php

/**
 * 活动功能
 */
// $sUrlDir = 'events';
// Route::group(['prefix' => $sUrlDir], function () {
//     $resource = 'events';
//     $controller = 'BankController@';
//     Route::get( '/', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
// });
Route::any('events', ['as' => 'liftRules.index', function () {
        return View::make('events.index');
    }]);
//
Route::group(['prefix' => 'reserve-agents'], function () {
    $resource = 'reserve-agents';
    $controller = 'ReserveAgentController@';
    Route::get('/', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::get('{id}/view', array('as' => $resource . '.view', 'uses' => $controller . 'view'));
    Route::any('/export', ['as' => $resource . '.export',      'uses' => $controller . 'download']);
});