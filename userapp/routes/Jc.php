<?php

# 投注
Route::group(['prefix' => 'jc'], function () {
    
    $resource = 'jc.';
    $controller = 'JcController\UserBetController@';

//    Route::any('/', ['as' => $resource . 'index', 'uses' => $controller . 'index']);
    
    Route::any('/bet', ['as' => $resource . 'bet', 'uses' => $controller . 'bet']);
    
    Route::any('/bet_list/{lottery_key}', ['as' => $resource . 'bet_list', 'uses' => $controller . 'bet_list']);
    Route::any('/bet_view/{bet_id}', ['as' => $resource . 'bet_view', 'uses' => $controller . 'bet_view']);
    Route::any('/bet_detail/{bet_id}', ['as' => $resource . 'bet_detail', 'uses' => $controller . 'bet_detail']);
    
    Route::any('/result/{lottery_key}/{method_type?}', ['as' => $resource . 'result', 'uses' => $controller . 'result']);
    
    Route::any('/confirm', ['as' => $resource . 'confirm', 'uses' => $controller . 'confirm']);
    Route::any('/help/{path?}', ['as' => $resource . 'help', 'uses' => $controller . 'help']);
});


# 合买
Route::group(['prefix' => 'jc'], function () {

    $resource = 'jc.';
    $controller = 'JcController\UserGroupBuyController@';

//    Route::any('/groupbuy', ['as' => $resource.'groupbuy', 'uses' => $controller . 'index']);
    Route::any('/groupbuy/{lottery_key}/{method_group_key?}', ['as' => $resource . 'groupbuy', 'uses' => $controller . 'groupbuy']);
    Route::any('/yutou/{lottery_key}', ['as' => $resource . 'yutou', 'uses' => $controller . 'yutou']);
    Route::any('/append/{group_id}', ['as' => $resource . 'append', 'uses' => $controller . 'append']);
    Route::any('/drop/{group_id}', ['as' => $resource . 'drop', 'uses' => $controller . 'drop']);
    Route::any('/follow/{group_id}', ['as' => $resource . 'follow', 'uses' => $controller . 'follow']);
    Route::any('/follow_list/{group_id}', ['as' => $resource . 'follow_list', 'uses' => $controller . 'follow_list']);
    
    Route::any('/drop_detail/{group_detail_id}', ['as' => $resource . 'drop_detail', 'uses' => $controller . 'drop_detail']);
    
    Route::any('/zj/{lottery_key}/{user_id}/{method_group_key?}', ['as' => $resource . 'zj', 'uses' => $controller . 'zj']);
});



Route::group(['prefix' => 'jc'], function () {
    
    $resource = 'jc.';
    $controller = 'JcController\UserBetController@';
    
    Route::any('/{lottery_key}/prize_detail', ['as' => $resource . 'prize_detail', 'uses' => $controller . 'prize_detail']);
    Route::any('/{lottery_key}/{method_key?}', ['as' => $resource . 'match_list', 'uses' => $controller . 'match_list']);
});