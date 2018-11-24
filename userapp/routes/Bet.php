<?php

# 投注
Route::group(['prefix' => 'bets'], function () {
    
    $resource = 'bets';
    $controller = 'BetController@';

    Route::any(      '/bet/{lottery_id?}', ['as' => $resource . '.bet','uses'       => $controller . 'bet']);
    Route::get(      '/bets/{series_id?}', ['as' => $resource . '.bets','uses'       => $controller . 'bets']);
    Route::get('/load-data/{lottery_id?}', ['as' => $resource . '.load-data','uses' => $controller . 'getGameSettingsForRefresh']);
    Route::get('/history-count/{lottery_id}/{issue_count?}', ['as' => $resource . '.history-count','uses' => $controller . 'getIssueHistoryCount']);
    Route::get('/trend-graph/{lottery_id}', ['as' => $resource . '.trend-graph','uses' => $controller . 'getTrendGraph']);
    Route::get(      '/upload-bet-number', ['as' => $resource . '.upload-bet-number', function (){
        return View::make('centerGame.uploadBetNumber');
    }]);

    Route::get(  'bet-info/{lottery_id?}', ['as' => $resource . '.bet-info', 'uses' => $controller . 'getUserProjectsAndTraces']);
    Route::post('/upload-bet-number', ['as' => $resource . '.upload-bet-number', 'uses' => function () {
//        pr(Input::all());
//        if (Request::getMethod() !== 'GET' && Session::token() != Input::get('_token')){
//            die('请先登录');
//        }
            $aLimits = [
                'extension' => [ 'txt'],
                'mime_type' => [ 'text/plain'],
                'max_size' => 1024 * 1024 * 3
            ];
            $aInputData = Input::all();
            $oFileInfo = $aInputData['betNumber'];
            in_array($oFileInfo->getClientOriginalExtension(), $aLimits['extension']) or die();
            in_array($oFileInfo->getClientMimeType(), $aLimits['mime_type']) or die();
            $oFileInfo->getClientSize() <= $aLimits['max_size'] or die();
            $rs = file_get_contents($oFileInfo->getPathName());
            echo '<script>(function(){var Games = window.parent.bomao.Games,current = Games.getCurrentGame().getCurrentGameMethod(),data=' . json_encode($rs) . '; current.getFile(data)})()</script>';
            exit;
    }]);
    
    Route::get( '/redoBet/{project_id?}',['as' => $resource.'.redo-bet', 'uses' => $controller . 'redoBet' ]);
    Route::get( '/wnnumber-history/{lottery_id}/{is_newest?}/{issue_count?}/{issue?}',['as' => $resource.'.wnnumber-history', 'uses' => $controller . 'getWnNumbersHistory' ]);
    Route::get( '/wnnumber-result',['as' => $resource.'.wnnumber-result', 'uses' => $controller . 'getWnnumberResult' ]);
    Route::get( '/profits/{series_id?}/{start_time?}/{end_time?}/{count?}',['as' => $resource.'.profits', 'uses' => $controller . 'getProfit' ]);

});
