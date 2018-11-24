<?php
Route::group(['prefix' => 'eurocups'], function () {
    
    $resource = 'eurocups';
    $controller = 'EuroCupController@';

    Route::any(      '/', ['as' => $resource . '.index','uses'       => $controller . 'index']);

});
