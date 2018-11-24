<?php

    /**
 * Way管理
 */
$sUrlDir = 'activity-rebate-settings';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'activity-rebate-settings';
    $controller = 'ActivityRebateSettingController@';
    Route::any('/index', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::any('create',array('as' => $resource . '.create','uses' => $controller . 'create'));
    Route::get('{id}/view', array('as' => $resource . '.view' , 'uses' => $controller . 'view'   ));
    Route::any('{id}/edit', array('as' => $resource . '.edit', 'uses' => $controller . 'edit')); // ->before('not.self');
    Route::get('{id}', array('as' => $resource . '.destroy', 'uses' => $controller . 'destroy')); // ->before('not.self');
});
