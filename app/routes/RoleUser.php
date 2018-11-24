<?php
    /**
 * 用户角色关联关系管理
 */
$sUrlDir = 'role-users';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'role-users';
    $controller = 'RoleUserController@';
    Route::get(           '/', ['as' => $resource . '.index',   'uses' => $controller . 'index']);
     Route::any(      'create/{role_id}', ['as' => $resource . '.create',  'uses' => $controller . 'create']);
    Route::get(   '{id}/view', ['as' => $resource . '.view',    'uses' => $controller . 'view']);
    Route::delete(     '{id}', ['as' => $resource . '.destroy', 'uses' => $controller . 'destroy']);
});