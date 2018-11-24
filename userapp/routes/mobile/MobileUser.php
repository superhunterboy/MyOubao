<?php

# User管理
Route::group(['prefix' => 'mobile-users'], function () {
    $resource = 'mobile-users';
    $controller = 'MobileUserController@';
    Route::any('/user-account-info', ['as' => $resource . '.user-account-info', 'uses' => $controller . 'getUserAccountInfo']);
    Route::any('safe-reset-fund-password', ['as' => $resource . '.safe-reset-fund-password', 'uses' => $controller . 'safeChangeFundPassword']);
    Route::any('password-management/{type?}', ['as' => $resource . '.password-management', 'uses' => $controller . 'passwordManagement']);
});
