<?php
/**
 * 审核类型管理
 */
$sUrlDir = 'audit-types';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'audit-types';
    $controller = 'AuditTypeController@';
    Route::get( '/', array('as' => $resource . '.index' , 'uses' => $controller . 'index'  ));
    Route::any('create/{cur_id?}', array('as' => $resource . '.create' , 'uses' => $controller . 'create' ));
    Route::any('{id}/view', array('as' => $resource . '.view' , 'uses' => $controller . 'view'   ));
    Route::any('{id}/edit', array('as' => $resource . '.edit' , 'uses' => $controller . 'edit'   )); // ->before('not.self');
    Route::delete( '{id}', array('as' => $resource . '.destroy', 'uses' => $controller . 'destroy')); // ->before('not.self');
});
/**
 * 审核管理
 */
$sUrlDir = 'audit-lists';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'audit-lists';
    $controller = 'AuditListController@';

    Route::get( '/', array('as' => $resource . '.index', 'uses' => $controller . 'index'  ));
    Route::get( '{id}/audit', array('as' => $resource . '.audit', 'uses' => $controller . 'audit'));
    Route::get('{id}/reject', array('as' => $resource . '.reject', 'uses' => $controller . 'reject'));
    Route::get('{id}/cancel', array('as' => $resource . '.cancel', 'uses' => $controller . 'cancel'));
});


$sUrlDir = 'audit-list-fund-passwords';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'audit-list-fund-passwords';
    $controller = 'AuditListFundPasswordController@';
    Route::get( '/', array('as' => $resource . '.index', 'uses' => $controller . 'index'  ));
    Route::get( '{id}/audit', array('as' => $resource . '.audit', 'uses' => $controller . 'audit'));
    Route::get('{id}/reject', array('as' => $resource . '.reject', 'uses' => $controller . 'reject'));
    Route::get('{id}/cancel', array('as' => $resource . '.cancel', 'uses' => $controller . 'cancel'));
});