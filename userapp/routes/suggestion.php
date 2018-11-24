<?php
/**
 * 客户建议
 */
Route::group(['prefix' => 'suggestions'], function () {
    $resource = 'suggestions';
    $controller = 'SuggestionController@';
    Route::post( 'create', ['as' => $resource . '.create', 'uses' => $controller . 'create']);
});
