<?php
    /**
 * 管理员管理
 */
$sUrlDir = 'admins';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'admin-users';
    $controller = 'AdminUserController@';
    Route::get(          '/', ['as' => $resource . '.index',          'uses' => $controller . 'index'  ]);
    Route::any(   'create/{cur_id?}', ['as' => $resource . '.create',         'uses' => $controller . 'create' ]);
    Route::any(          '{id}/view', ['as' => $resource . '.view',           'uses' => $controller . 'view'   ]);
    Route::any(          '{id}/edit', ['as' => $resource . '.edit',           'uses' => $controller . 'edit'   ]);
    Route::delete(    '{id}', ['as' => $resource . '.destroy',        'uses' => $controller . 'destroy']);
    Route::any(    'change-password', ['as' => $resource . '.change-password', 'uses' => $controller . 'changePassword']);
    Route::any('{id}/reset-password', ['as' => $resource . '.reset-password', 'uses' => $controller . 'resetPassword']);
});
