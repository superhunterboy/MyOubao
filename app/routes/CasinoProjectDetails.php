<?php
/**
 * 注单管理
 */
$sUrlDir = 'casino-project-details';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'casino-project-details';
    $controller = 'CasinoProjectDetailController@';
    Route::get('/', ['as' => $resource . '.index' , 'uses' => $controller . 'index'  ]);
});