<?php
/**
 * 用户，银行卡关联关系管理
 */
$sUrlDir = 'user-bank-cards';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'user-bank-cards';
    $controller = 'UserBankCardController@';
    Route::get( 		'/', ['as' => $resource . '.index',  'uses' => $controller . 'index']);
    Route::any('create/{id?}', ['as' => $resource . '.create', 'uses' => $controller . 'create']);
    Route::any( '{id}/view', ['as' => $resource . '.view',   'uses' => $controller . 'view']);
    Route::any( '{id}/edit', ['as' => $resource . '.edit',   'uses' => $controller . 'edit']);
    Route::delete(   '{id}', ['as' => $resource . '.destroy', 'uses' => $controller . 'destroy']);
    Route::put(  '{id}/lock-bankcards', ['as' => $resource . '.lock-bankcards',      'uses' => $controller . 'lockUserBankCards']);
    Route::put('{id}/unlock-bankcards', ['as' => $resource . '.unlock-bankcards',    'uses' => $controller . 'unlockUserBankCards']);
    Route::any(  'searchUserByAccount', ['as' => $resource . '.searchUserByAccount', 'uses' => $controller . 'searchUserByAccount']);
    Route::get(           '/download', ['as' => $resource . '.download',   'uses' => $controller . 'download']);
});