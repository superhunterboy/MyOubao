<?php

/**
 * 链接开户管理
 */
$sUrlDir = 'register-links';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'register-links';
    $controller = 'RegisterLinkController@';
    Route::get(         '/', ['as' => $resource . '.index',   'uses' => $controller . 'index']);
    Route::any(    'create', ['as' => $resource . '.create',  'uses' => $controller . 'create']);
    Route::get( '{id}/view', ['as' => $resource . '.view',    'uses' => $controller . 'view']);
    Route::any( '{id}/edit', ['as' => $resource . '.edit',    'uses' => $controller . 'edit'])->before('topAgent');
    Route::delete(   '{id}', ['as' => $resource . '.destroy', 'uses' => $controller . 'destroy']);
    Route::get('{id}/close', ['as' => $resource . '.close',   'uses' => $controller . 'closeLink']);
});