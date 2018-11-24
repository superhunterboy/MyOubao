<?php
/**
 * 代理和普通用户管理
 */
$sUrlDir = 'user-user-commission-sets';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'user-user-commission-sets';
    $controller = 'UserUserCommissionSetController@';
    Route::get(               '/', ['as' => $resource . '.index' ,  'uses' => $controller . 'index'  ]);
    Route::get(         'diff-commission-rate/{id}', ['as' => $resource . '.diff-commission-rate',          'uses' => $controller . 'diffCommissionRate']);
});