<?php

# 安全口令
Route::group(['prefix' => 'security-questions'], function () {
    $resource = 'security-questions';
    $controller = 'SecurityQuestionsController@';
    Route::get(      '/', ['as' => $resource . '.index', 'uses' => $controller . 'index']);
    Route::any(   'create', ['as' => $resource . '.create',  'uses' => $controller . 'create']);
    Route::get('{id}/view', ['as' => $resource . '.view',    'uses' => $controller . 'view']);
    Route::any('{id}/edit', ['as' => $resource . '.edit',    'uses' => $controller . 'edit']);
    Route::delete(  '{id}', ['as' => $resource . '.destroy', 'uses' => $controller . 'destroy']);
    Route::post(  'checkrules', ['as' => $resource . '.checkrules', 'uses' => $controller . 'checkrule']);
    Route::post(  'savedata', ['as' => $resource . '.savedata', 'uses' => $controller . 'savedata']);
    Route::any(  'checksecurityanswer', ['as' => $resource . '.checksecurityanswer', 'uses' => $controller . 'checksecurityanswer']);
});