<?php

/**
 * 域名管理
 */
$sUrlDir = 'agent-domains';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'agent-domains';
    $controller = 'AgentDomainController@';
    Route::get( '/', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::any( 'create', array('as' => $resource . '.create', 'uses' => $controller . 'create'));
    Route::get('{id}/view', array('as' => $resource . '.view' , 'uses' => $controller . 'view'   ));
    Route::any('{id}/edit', array('as' => $resource . '.edit', 'uses' => $controller . 'edit')); // ->before('not.self');
    Route::delete('{id}', array('as' => $resource . '.destroy', 'uses' => $controller . 'destroy')); // ->before('not.self');
});

$sUrlDir = 'agent-domain-groups';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'agent-domain-groups';
    $controller = 'AgentDomainGroupController@';

    Route::get( '/', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::get( 'index', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::any( 'create', array('as' => $resource . '.create', 'uses' => $controller . 'create'));
    Route::get('{id}/view', array('as' => $resource . '.view' , 'uses' => $controller . 'view'   ));
    Route::any('{id}/edit', array('as' => $resource . '.edit', 'uses' => $controller . 'edit')); // ->before('not.self');
    Route::delete('{id}', array('as' => $resource . '.destroy', 'uses' => $controller . 'destroy')); // ->before('not.self');
});


$sUrlDir = 'agent-domain-users';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'agent-domain-users';
    $controller = 'AgentDomainUserController@';
    Route::get( '/', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::any( 'create/{id}', array('as' => $resource . '.create', 'uses' => $controller . 'createNew'));
    Route::get('{id}/view', array('as' => $resource . '.view' , 'uses' => $controller . 'view'   ));
    Route::any('{id}/edit', array('as' => $resource . '.edit', 'uses' => $controller . 'edit')); // ->before('not.self');
    Route::delete('{id}', array('as' => $resource . '.destroy', 'uses' => $controller . 'destroy')); // ->before('not.self');
});

