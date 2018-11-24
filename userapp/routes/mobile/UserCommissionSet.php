<?php
/**
 * 代理和普通用户管理
 */
$sUrlDir = 'user-commission-sets';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'user-commission-sets';
    $controller = 'UserCommissionSetController@';
    Route::get(               '/', ['as' => $resource . '.index' ,  'uses' => $controller . 'index'  ]);
    Route::any(         '{id}/edit', ['as' => $resource . '.edit',          'uses' => $controller . 'edit']);
    Route::get(         '{id}/diff-commission-rate', ['as' => $resource . '.diff-commission-rate',          'uses' => $controller . 'diffCommissionRate']);
});