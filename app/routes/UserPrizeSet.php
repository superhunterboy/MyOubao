<?php

/**
 * UserPrizeSet管理
 */
$sUrlDir = 'user-prize-sets';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'user-prize-sets';
    $controller = 'UserPrizeSetController@';
    Route::get(    '/index', ['as' => $resource . '.index',         'uses' => $controller . 'index']);
    // Route::get(        'agent-distribution', ['as' => $resource . '.agent-distribution',    'uses' => $controller . 'agentDistributionList']);
    // Route::get(         'agent-prize-group', ['as' => $resource . '.agent-prize-group',     'uses' => $controller . 'agentPrizeGroupList']);
    Route::any('set-agent-prize-group/{id}', ['as' => $resource . '.set-agent-prize-group', 'uses' => $controller . 'setPrizeGroupForAgent']);
//    Route::any(           'create', ['as' => $resource . '.create',     'uses' => $controller . 'create']);
//    Route::get(        '{id}/view', ['as' => $resource . '.view' ,      'uses' => $controller . 'view'   ]);
    Route::any(         '{id}/edit', ['as' => $resource . '.edit',          'uses' => $controller . 'edit']);
//    Route::delete(          '{id}', ['as' => $resource . '.destroy',    'uses' => $controller . 'destroy']);
});
