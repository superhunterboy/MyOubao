<?php

/**
 * Article管理
 */
$sUrlDir = 'cms-articles';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'cms-articles';
    $controller = 'CmsArticleController@';
    Route::get(              '/', ['as' => $resource . '.index',      'uses' => $controller . 'index']);
    Route::any(         'create', ['as' => $resource . '.create',     'uses' => $controller . 'create']);
    Route::get(      '{id}/view', ['as' => $resource . '.view',       'uses' => $controller . 'view']);
    Route::get(    '{id}/cancel', ['as' => $resource . '.cancel',     'uses' => $controller . 'cancelArticle']);
    Route::get(       '{id}/top', ['as' => $resource . '.top',        'uses' => $controller . 'topArticle']);
    Route::get('{id}/cancel-top', ['as' => $resource . '.cancel-top', 'uses' => $controller . 'cancelTopArticle']);
    Route::get(     '{id}/audit', ['as' => $resource . '.audit',      'uses' => $controller . 'audit']);
    Route::get(    '{id}/reject', ['as' => $resource . '.reject',     'uses' => $controller . 'reject']);
    Route::any(      '{id}/edit', ['as' => $resource . '.edit',       'uses' => $controller . 'edit']);
    Route::delete(        '{id}', ['as' => $resource . '.destroy',    'uses' => $controller . 'destroy']);
});
