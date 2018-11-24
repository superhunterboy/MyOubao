<?php

#系统设置管理 | System Settings Management
Route::group(['prefix' => 'sys-configs'], function () {
    Route::get('/', array('as' => 'sys-configs.index', 'uses' => 'SysConfigController@index'));
    Route::get('settings', array('as' => 'sys-configs.settings', 'uses' => 'SysConfigController@settings'));
    Route::any('create/{cur_id?}', array('as' => 'sys-configs.create', 'uses' => 'SysConfigController@create'));
    Route::any('{id}/view', array('as' => 'sys-configs.view', 'uses' => 'SysConfigController@view'));
    Route::any('{id}/edit', array('as' => 'sys-configs.edit', 'uses' => 'SysConfigController@edit'));
    Route::delete('{id}', array('as' => 'sys-configs.destroy', 'uses' => 'SysConfigController@destroy'));
    Route::post('set-order', array('as' => 'sys-configs.set-order', 'uses' => 'SysConfigController@setOrder'));
    Route::any('set-value/{id?}', array('as' => 'sys-configs.set-value', 'uses' => 'SysConfigController@setValue'));
});
