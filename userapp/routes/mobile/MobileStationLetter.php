<?php

# 站内信
Route::group(['prefix' => 'mobile-station-letters'], function () {
    $resource = 'mobile-station-letters';
    $controller = 'MobileStationLetterController@';
    Route::any('/', ['as' => $resource . '.index', 'uses' => $controller . 'index']);
    Route::any('{id}/view', ['as' => $resource . '.view', 'uses' => $controller . 'viewMessage']);
});
