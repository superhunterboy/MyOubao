<?php
use Illuminate\Support\Facades\View;

//---------------//
//-活动总路由文件--//
//---------------//

//-----------------现在为静态路由----------------------
//幸运猫路由
//Route::group(['prefix' => 'luckycat'], function () {
//    $resource = 'luckycat';
//    $controller = 'LuckyCatController@';
//
//    if(empty(Session::get('user_id')))
//    {
//        return View::make('events.luckycat.static');
//    }
//    
//    Route::any('/', ['as' => $resource . '.index',  'uses' => $controller . 'index']);
//    Route::any('sicifan', ['as' => $resource . '.sicifan',  'uses' => $controller . 'sicifan']);
//    Route::any('yicifan', ['as' => $resource . '.yicifan',  'uses' => $controller . 'yicifan']);
//    Route::any('winprize', ['as' => $resource . '.winprize',  'uses' => $controller . 'winprize']);
//    Route::any('myprizes', ['as' => $resource . '.myprizes',  'uses' => $controller . 'myprizes']);
//    Route::any('my-deposit-prizes', ['as' => $resource . '.myDepositPrizes',  'uses' => $controller . 'myDepositPrizes']);
//
//});

# For 流水 activity
Route::group(['prefix' => 'transaction-flow'], function () {

    $resource = 'transaction-flow';
    $controller = 'TransactionFlowController@';

    //Route::resource('transaction-flow','TransactionFlowController');
    
    Route::any('/', [
        'as' => $resource . '.index',  
        'uses' => $controller . 'index'
    ]);

    Route::any('/update', [
        'as' => $resource . '.update',  
        'uses' => $controller . 'update'
    ]);
    

});


//-移动端安装包下载--//
Route::group(['prefix' => 'mobile-download'], function () {
    $resource = 'mobile-download';
    $controller = 'MobileDownloadController@';
    Route::get( '/', ['as' => $resource . '.download', 'uses' => $controller . 'download']);
});

Route::group(['prefix' => 'xy28'], function () {
    $resource = 'xy28';
    $controller = 'Xy28Controller@';
    Route::get( '/', ['as' => $resource . '.index', 'uses' => $controller . 'index']);
});
