<?php


/**
 * Account管理
 */
$sUrlDir = 'dictionaries';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'dictionaries';
    $controller = 'DictionaryController@';
    Route::get('/index', array('as' => $resource . '.index', 'uses' => $controller . 'index'));
    Route::any('create/{id?}', array('as' => $resource . '.create', 'uses' => $controller . 'create'));
    Route::get('{id}/view', array('as' => $resource . '.view', 'uses' => $controller . 'view'));
    Route::any('{id}/edit', array('as' => $resource . '.edit', 'uses' => $controller . 'edit')); // ->before('not.self');
    Route::delete('{id}', array('as' => $resource . '.destroy', 'uses' => $controller . 'destroy')); // ->before('not.self');
    Route::get('/generate/{id?}', array('as' => $resource . '.generate', 'uses' => $controller . 'generate'));
    Route::get('/generate-all', array('as' => $resource . '.generate-all', 'uses' => $controller . 'generateAll'));
    Route::get('/create-from-models',array('as' => $resource . '.create-from-models','uses' => $controller . 'createFromModels'));
});
