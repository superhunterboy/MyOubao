<?php
/**
 * 管理员, 角色关联关系管理
 */
$sUrlDir = 'admin-role-users';
Route::group(['prefix' => $sUrlDir], function() {
    $resource = 'admin-role-users';
    $controller = 'AdminRoleUserController@';
    Route::get(       '/', ['as' => $resource . '.index',   'uses' => $controller . 'index']);
    Route::any('create/{role_id}', ['as' => $resource . '.create',  'uses' => $controller . 'create']);
    Route::any(       '{id}/view', ['as' => $resource . '.view',    'uses' => $controller . 'view']);
    Route::delete(         '{id}', ['as' => $resource . '.destroy', 'uses' => $controller . 'destroy']);
});