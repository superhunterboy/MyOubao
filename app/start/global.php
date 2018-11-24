<?php
define('DS',DIRECTORY_SEPARATOR);
/*
|--------------------------------------------------------------------------
| Register The Laravel Class Loader
|--------------------------------------------------------------------------
|
| In addition to using Composer, you may use the Laravel class loader to
| load your controllers and models. This is useful for keeping all of
| your classes in the "global" namespace without Composer updating.
|
*/

//$sPublicModelsPath = realpath(app_path().'/../models');
//$sPattern = $sPublicModelsPath .'/*';
//$aModelPaths = glob($sPattern, GLOB_MARK);
//$aPaths = [];
//foreach($aModelPaths as $sPath){
//    if (substr($sPath,-1) == '/'){
//        $aPaths[] = app_path() . '/../models/' . basename($sPath);
//    }
//}
//empty($aPaths) or ClassLoader::addDirectories($aPaths);
//unset($aPaths);

ClassLoader::addDirectories(array(

	app_path().'/commands',
	app_path().'/controllers',
	app_path().'/models',
//	app_path().'/../models',
	app_path().'/database/seeds',
	app_path().'/routes',
	app_path().'/lib',

));

//pr(ClassLoader::getDirectories());
//exit;

/*
|--------------------------------------------------------------------------
| Application Error Logger
|--------------------------------------------------------------------------
|
| Here we will configure the error logger setup for the application which
| is built on top of the wonderful Monolog library. By default we will
| build a basic log file setup which creates a single file for logs.
|
*/

Log::useFiles(storage_path().'/logs/laravel.log');

/*
|--------------------------------------------------------------------------
| Application Error Handler
|--------------------------------------------------------------------------
|
| Here you may handle any errors that occur in your application, including
| logging them or displaying custom views for specific errors. You may
| even register several error handlers to handle different types of
| exceptions. If nothing is returned, the default error view is
| shown, which includes a detailed stack trace during debug.
|
*/

App::error(function(Exception $exception, $code){

    // Careful here, any codes which are not specified
    // will be treated as 500

    if ( ! in_array($code, [401,403,404,500])){
       return;
    }

    // assumes you have app/views/errors/401.blade.php, etc
    $view = "system/$code";

    // add data that you want to pass to the view
    $data = ['code' => $code];

    // switch statements provided in case you need to add
    // additional logic for specific error code.
    Log::error($exception);
    switch ($code) {
       case 401:
       return Response::view($view, $data, $code);

       case 403:
       return Response::view($view, $data, $code);

       case 404:
       return Response::view($view, $data, $code);

       // TODO debug period comment
       // case 500:
       // return Response::view($view, $data, $code);
   }
});

/*
|--------------------------------------------------------------------------
| Maintenance Mode Handler
|--------------------------------------------------------------------------
|
| The "down" Artisan command gives you the ability to put an application
| into maintenance mode. Here, you will define what is displayed back
| to the user if maintenance mode is in effect for the application.
|
*/

App::down(function()
{
	return Response::make("Be right back!", 503);
});

/*
|--------------------------------------------------------------------------
| Require The Filters File
|--------------------------------------------------------------------------
|
| Next we will load the filters file for the application. This gives us
| a nice separate location to store our route and application filter
| definitions instead of putting them all in the main routes file.
|
*/

require app_path().'/filters.php';
