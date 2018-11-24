<?php
/**
 * 管理员角色管理
 */
$sUrlDir = 'admin-roles';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'admin-roles';
    $controller = 'AdminRoleController@';
    Route::get(        '/', ['as' => $resource . '.index',       'uses' => $controller . 'index']);
    Route::any( 'create/{cur_id?}', ['as' => $resource . '.create',      'uses' => $controller . 'create']);
    Route::any(        '{id}/view', ['as' => $resource . '.view',        'uses' => $controller . 'view']);
    Route::any(        '{id}/edit', ['as' => $resource . '.edit',        'uses' => $controller . 'edit']);
    Route::delete(          '{id}', ['as' => $resource . '.destroy',     'uses' => $controller . 'destroy']);
    Route::any(  'bind-user/{id?}', ['as' => $resource . '.bind-user',   'uses' => $controller . 'bindUser']);
    Route::any( '{id}/view-rights', ['as' => $resource . '.view-rights', 'uses' => $controller . 'viewRights']);
    Route::any(  '{id}/set-rights', ['as' => $resource . '.set-rights',  'uses' => $controller . 'setRights']);
    Route::post('set-order', array('as' => $resource . '.set-order', 'uses' => $controller . 'setOrder'));
});

