<?php
#配额管理
Route::group(['prefix'=>    'my-overlimit-quotas'],function (){
    $resource   =   'my-overlimit-quotas';
    
    $controller =   'MyOverLimitQuotaController@';
    Route::get('/index/{user_id?}',['as'=>$resource.'.index','uses'=>$controller."index"]);
    Route::get('/get-quota-and-history', ['as' => $resource . '.get-quota-and-history', 'uses' =>  $controller.'getQuotaAndHistory']);
    Route::post('/save',                ['as'  => $resource . '.save',                  'uses' =>  $controller.'save']);
});

