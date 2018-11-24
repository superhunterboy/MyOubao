<?php
/**
 * 注单管理
 */
$sUrlDir = 'casino-projects';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'casino-projects';
    $controller = 'CasinoProjectController@';
    Route::get('/', ['as' => $resource . '.index' , 'uses' => $controller . 'index'  ]);
    Route::get('/cancel/{id?}', ['as' => $resource . '.cancel' , 'uses' => $controller . 'cancelGame'  ]);
});
$sUrlDir='black-jack-tables';
Route::group([], function () {
    $controller = 'BlackJackTableController@';
    $resource = 'black-jack-tables';
    Route::get('/table/index', ['as' => $resource . '.index' , 'uses' => $controller . 'index'  ]);
    Route::any('/table/edit/{id}', ['as' => $resource . '.edit' , 'uses' => $controller . 'edit'  ]);
});

$sUrlDir='black-jack-jacpots';
Route::group([], function () {
    $controller = 'BlackJackJacpotController@';
    $resource = 'black-jack-jacpots';
    Route::get('/jacpot/index', ['as' => $resource . '.index' , 'uses' => $controller . 'index'  ]);
    Route::any('/jacpot/edit/{id}', ['as' => $resource . '.edit' , 'uses' => $controller . 'edit'  ]);
});
