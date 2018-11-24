<?php

/**
 * Class MaxAccessFilter - 访问量过滤器
 *
 * 用来控制单个路由，单IP访问量过大的问题。参数使用方法如例子所示（以下方法控制访问首页每分钟不能超过10次）
 *
 * filter.php设置
 * Route::filter('maxAccess', 'MaxAccessFilter');
 * route.php 设置
 * Route::get('/', array('as' => 'home', 'uses' => 'HomeController@showWelcome', 'before'=>'maxAccess:1,10'));
 *
 * @author Johnny <Johnny@anvo.com>
 * @date 2014-11-26 18:02
 *
 */
class MaxAccessFilter
{
    /**
     * 过滤掉制定路由，超过制定$minutes分钟内，单个IP超过$times的请求
     *
     * @param $route 路由类
     * @param $request 请求累
     * @param int $minutes 分钟数
     * @param int $times 次数
     */
    public function filter($route, $request, $minutes=10, $times=600)
    {
        // pr($minutes);
        // pr($times);
        // exit;
        $key    = '__MaxAccessFilter__'.Request::getClientIp().'__'.md5($route);

        if(($mtimes  = Cache::get($key)) != false)
        {
            if ($mtimes >= $times)
            {
                Log::debug("访问量控制器提醒您：该路由您已超出指定访问次数，请稍后重试！");
                App::abort('403', '您的访问过于频繁，请稍后重试！');
            }
            Cache::increment($key);
        }
        else
        {
            Cache::put($key, 1, $minutes);
        }
        Log::debug("访问量控制器提醒您：您已经访问了该路由 ".(intval($mtimes) + 1)." 次！ 剩余访问机会：".($times - $mtimes - 1) ."次！");
    }
}