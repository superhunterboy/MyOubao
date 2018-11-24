<?php

/**
 * Issue管理
 */
$sUrlDir = 'issues';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'issues';
    $controller = 'IssueController@';
    Route::get(         '/index', ['as' => $resource . '.index',         'uses' => $controller . 'index']);
    Route::any('/generate/{lottery_id?}', ['as' => $resource . '.generateStep1', 'uses' => $controller . 'generate']);
//  Route::any(         '/generateStep2', ['as' => $resource . '.generateStep2', 'uses' => $controller . 'generateStep2']);
//  Route::any(         '/generateStep3', ['as' => $resource . '.generateStep3', 'uses' => $controller . 'generateStep3']);
    Route::any(    'batch-delete/{lottery_id?}', ['as' => $resource . '.batch-delete',   'uses' => $controller . 'batchDelete']);
    Route::any(       'download', ['as' => $resource . '.download',      'uses' => $controller . 'download']);
    Route::any(         'create', ['as' => $resource . '.create',        'uses' => $controller . 'create']);
    Route::get(      '{id}/view', ['as' => $resource . '.view',          'uses' => $controller . 'view']);
    Route::any(      '{id}/edit', ['as' => $resource . '.edit',          'uses' => $controller . 'edit']);
    Route::delete(        '{id}', ['as' => $resource . '.destroy',       'uses' => $controller . 'destroy']);
    Route::any(   'encode/{lottery_id?}', ['as' => $resource . '.encode',        'uses' => $controller . 'encode']);
    Route::get('batch-calculate/{id?}',['as' => $resource . '.batch-calculate','uses' => $controller . 'setCalculateTaskBatch']);
    Route::get('set-calculate/{id?}',['as' => $resource . '.set-calculate','uses' => $controller . 'setCalculateTask']);
    Route::get('set-cancel/{id?}',['as' => $resource . '.set-cancel','uses' => $controller . 'setCancelTask']);
    Route::get('{lottery_id}/generate-next-issue',['as' => $resource . '.generate-next-issue','uses' => $controller . 'generateNextIssue']);
});

/**
 * RestSetting管理
 */
$sUrlDir = 'rest-settings';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'rest-settings';
    $controller = 'RestSettingController@';
    Route::get(      '/index', ['as' => $resource . '.index',   'uses' => $controller . 'index']);
    Route::any('create/{id?}', ['as' => $resource . '.create',  'uses' => $controller . 'create']);
    Route::get(   '{id}/view', ['as' => $resource . '.view',    'uses' => $controller . 'view']);
    Route::any(   '{id}/edit', ['as' => $resource . '.edit',    'uses' => $controller . 'edit']);
    Route::delete(     '{id}', ['as' => $resource . '.destroy', 'uses' => $controller . 'destroy']);
});

