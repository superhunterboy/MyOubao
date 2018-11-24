<?php

# User管理
Route::group(['prefix' => 'users'], function () {
    $resource   = 'users';
    $controller = 'UserUserController@';
    Route::any(                          '/', ['as' => $resource . '.index',                    'uses' => $controller . 'index'])->before('agent');
    Route::get(            '{pid}/sub-users', ['as' => $resource . '.sub-users',                'uses' => $controller . 'subUsers'])->before('agent');
    Route::any('password-management/{type?}', ['as' => $resource . '.password-management',      'uses' => $controller . 'passwordManagement']);
    Route::any(   'safe-reset-fund-password', ['as' => $resource . '.safe-reset-fund-password', 'uses' => $controller . 'safeChangeFundPassword']);
    Route::any(            'accurate-create', ['as' => $resource . '.accurate-create',          'uses' => $controller . 'accurateCreate'])->before('agent');
    Route::any(                   'personal', ['as' => $resource . '.personal',                 'uses' => $controller . 'personal']);
    Route::any(                 'bind-email', ['as' => $resource . '.bind-email',               'uses' => $controller . 'bindEmail']);
    Route::any(             'activate-email', ['as' => $resource . '.activate-email',           'uses' => $controller . 'activateEmail', 'before'=>'maxAccess:1,10']);
    Route::get(         'user-monetary-info', ['as' => $resource . '.user-monetary-info',       'uses' => $controller . 'getLoginUserMonetaryInfo'])->before('ajax');
    Route::get(          'user-account-info', ['as' => $resource . '.user-account-info',        'uses' => $controller . 'getLatestUserAccountInfo']); // ->before('ajax')
});