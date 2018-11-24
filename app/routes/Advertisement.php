<?php


/**
 * 广告管理
 */
$sUrlDir = 'ad-locations';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'ad-locations';
    $controller = 'AdLocationsController@';
    Route::get('/index', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::any('create/{id?}', array('as' => $resource . '.create', 'uses' => $controller . 'create'));
    Route::get('{id}/view', array('as' => $resource . '.view', 'uses' => $controller . 'view'));
    Route::any('{id}/edit', array('as' => $resource . '.edit', 'uses' => $controller . 'edit')); // ->before('not.self');
    Route::delete('{id}', array('as' => $resource . '.destroy', 'uses' => $controller . 'destroy')); // ->before('not.self');
    Route::get('/generate/{id?}', array('as' => $resource . '.generate', 'uses' => $controller . 'generate'));
    //Route::get('{id}/info', array('as' => $resource . '.info', 'uses' => $controller . 'info'));

        });

/**
 * 广告管理 type
 */
$sUrlDir = 'ad-types';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'ad-types';
    $controller = 'AdTypeController@';
    Route::get('/index', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::any('create/{id?}', array('as' => $resource . '.create', 'uses' => $controller . 'create'));
    Route::get('{id}/view', array('as' => $resource . '.view', 'uses' => $controller . 'view'));
    Route::any('{id}/edit', array('as' => $resource . '.edit', 'uses' => $controller . 'edit')); // ->before('not.self');
    Route::delete('{id}', array('as' => $resource . '.destroy', 'uses' => $controller . 'destroy')); // ->before('not.self');
        });

/**
 * 广告管理 info
 */
$sUrlDir = 'ad-infos';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'ad-infos';
    $controller = 'AdInfoController@';
    Route::get('/index/{ad_location_id?}', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::get('upload-image', array('as' => $resource . '.upload-image', 'uses' => $controller . 'uploadImages'));
    Route::any('create/{id?}', array('as' => $resource . '.create', 'uses' => $controller . 'create'));
    Route::get('{id}/view', array('as' => $resource . '.view', 'uses' => $controller . 'view'));
    Route::any('{id}/edit', array('as' => $resource . '.edit', 'uses' => $controller . 'edit')); // ->before('not.self');
    Route::delete('{id}', array('as' => $resource . '.destroy', 'uses' => $controller . 'destroy')); // ->before('not.self');
        });
