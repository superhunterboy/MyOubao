<?php
    /**
 * Search管理
 */
    $sUrlDir = 'search-configs';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'search-configs';
        $controller = 'SearchConfigController@';
        Route::get( '/', array('as' => $resource . '.index' , 'uses' => $controller . 'index'  ));
        Route::get( '/list', array('as' => $resource . '.list' , 'uses' => $controller . 'listSearchConfig'  ));
        // Route::get( '{id}/sub', array('as' => $resource . '.sub' , 'uses' => $controller . 'sub'  ));
        Route::any('create/{cur_id?}', array('as' => $resource . '.create' , 'uses' => $controller . 'create' ));
        Route::any('{id}/view', array('as' => $resource . '.view' , 'uses' => $controller . 'view'   ));
        Route::any( '{id}/edit', array('as' => $resource . '.edit' , 'uses' => $controller . 'edit'   ));
        Route::delete( '{id}', array('as' => $resource . '.destroy', 'uses' => $controller . 'destroy'));
    });
