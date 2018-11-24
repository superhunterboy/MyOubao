<?php namespace MongoCache;

use Illuminate\Support\ServiceProvider;

class CacheServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['cache'] = $this->app->share(function($app)
		{
			return new MongoCacheManager($app);
		});

        $this->app->bindShared('cache.store', function($app)
        {
            return $app['cache']->driver();
        });

		$this->app['memcached.connector'] = $this->app->share(function()
		{
			return new MemcachedConnector;
		});
	}

    /**
     * Register the cache related console commands.
     *
     * @return void
     */
    public function registerCommands()
    {
        $this->app->bindShared('command.cache.clear', function($app)
        {
            return new Console\ClearCommand($app['cache'], $app['files']);
        });

        $this->commands('command.cache.clear');
    }

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		// return array('cache', 'memcached.connector');
        return array('cache', 'cache.store', 'memcached.connector', 'command.cache.clear');
	}

}