<?php
    /**
 * 代理和普通用户管理
 */
$sUrlDir = 'users';
Route::group(['prefix' => $sUrlDir], function () {
    $resource = 'users';
    $controller = 'UserController@';
    Route::get(               '/', ['as' => $resource . '.index' ,  'uses' => $controller . 'index'  ]);
    // Route::get('search-users-list', ['as' => $resource . '.search-users-list' , 'uses' => $controller . 'searchUserList' ]);
    Route::any('create/{cur_id?}', ['as' => $resource . '.create' , 'uses' => $controller . 'create' ]);
    Route::any(       '{id}/view', ['as' => $resource . '.view' ,   'uses' => $controller . 'view'   ]);
    // Route::any(       '{id}/edit', ['as' => $resource . '.edit' ,   'uses' => $controller . 'edit'   ]); // be disabled
//    Route::delete(         '{id}', ['as' => $resource . '.destroy', 'uses' => $controller . 'destroy']);
    
    Route::delete(        '{id}', ['as' => $resource . '.destroy',    'uses' => $controller . 'destroy']);
    Route::get(       'agent-distribution', ['as' => $resource . '.agent-distribution',    'uses' => $controller . 'agentDistributionList']);
    Route::get(        'agent-prize-group', ['as' => $resource . '.agent-prize-group',     'uses' => $controller . 'agentPrizeGroupList']);
    Route::get(    'agent-group-info/{id}', ['as' => $resource . '.agent-group-info',      'uses' => $controller . 'agentGroupAccountInfo']);
    Route::any(         '{id}/reset-fund-password', ['as' => $resource . '.reset-fund-password',          'uses' => $controller . 'resetFundPassword']);
    Route::any(      '{id}/reset-password', ['as' => $resource . '.reset-password',       'uses' => $controller . 'resetPassword']);
    Route::any(          '{id}/block-user', ['as' => $resource . '.block-user',           'uses' => $controller . 'blockUser']);
    Route::any(        '{id}/unblock-user', ['as' => $resource . '.unblock-user',         'uses' => $controller . 'unblockUser']);
    Route::get(    '{id}/up-points-blacklist-user', ['as' => $resource . '.up-points-blacklist-user',     'uses' => $controller . 'addUserToUpPointsBlackList']);
    Route::get(  '{id}/down-points-blacklist-user', ['as' => $resource . '.down-points-blacklist-user',   'uses' => $controller . 'addUserToDownPointsBlackList']);
    Route::get(     '{id}/dividend-blacklist-user', ['as' => $resource . '.dividend-blacklist-user',      'uses' => $controller . 'addUserToDividendBlackList']);
    Route::put(   '{id}/withdrawal-whitelist-user', ['as' => $resource . '.withdrawal-whitelist-user',    'uses' => $controller . 'addUserToWithdrawalWhiteList']);
    Route::put(   '{id}/withdrawal-blacklist-user', ['as' => $resource . '.withdrawal-black-user',        'uses' => $controller . 'addUserToWithdrawalBlackList']);
    Route::put('{id}/icbc-recharge-whitelist-user', ['as' => $resource . '.icbc-recharge-whitelist-user', 'uses' => $controller . 'addUserToICBCRechargeWhiteList']);
    Route::any(   '{id}/user-deposit', ['as' => $resource . '.user-deposit',  'uses' => $controller . 'deposit']);
    Route::any(  '{id}/user-withdraw', ['as' => $resource . '.user-withdraw', 'uses' => $controller . 'withdraw']);
    Route::get(           '/download', ['as' => $resource . '.download',      'uses' => $controller . 'download']);

});
