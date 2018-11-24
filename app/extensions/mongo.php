<?php

use Illuminate\Cache\Repository;

Cache::extend('mongo', function($app)
{
    return new Repository(new MongoStore);
});