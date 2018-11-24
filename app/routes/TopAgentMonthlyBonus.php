<?php

Route::group(['prefix' => 'top-agent-monthly-bonus'], function () {
    $resource = 'top-agent-monthly-bonuses';
    $controller = 'TopAgentMonthlyBonusController@';
    Route::get('/', ['as' => $resource . '.index', 'uses' => $controller . 'index']);
    Route::get('{id}/view', ['as' => $resource . '.view', 'uses' => $controller . 'view']);
    Route::any('{id}/edit', array('as' => $resource . '.edit', 'uses' => $controller . 'edit'));
    Route::any('{id}/create', array('as' => $resource . '.create', 'uses' => $controller . 'create'));
});
