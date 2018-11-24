<?php

/*
|--------------------------------------------------------------------------
| 注册应用程序事件，执行顺序如下：
|--------------------------------------------------------------------------
|
| 1.执行 应用程序事件   App::before   参数 $request
| 2.执行 前置过滤器   Route::filter   参数 $route, $request
|
| 3.执行（之前注册进路由的）匿名回调函数或相应的控制器方法，并取得响应实例 $response
|
| 4.执行 后置过滤器   Route::filter   参数 $route, $request, $response
| 5.执行 应用程序事件   App::after    参数 $request, $response
|
| 6.向客户端返回响应实例 $response
|
| 7.执行 应用程序事件   App::finish   参数 $request, $response
| 8.执行 应用程序事件   App::shutdown 参数 $application
|
*/

# App::before(function ($request) {});

# App::after(function ($request, $response) {});

# App::finish(function ($request, $response) {});

# App::shutdown(function($application) {});


/*
|--------------------------------------------------------------------------
| [前置] 过滤器
|--------------------------------------------------------------------------
# Route::filter('beforeFilter', function ($route, $request) {});
|
*/
Route::filter('beforeFilter', function ($route, $request) {
//     if (Request::method() == 'GET' && str_contains(Route::currentRouteAction(), 'index') ) {
//         Session::put('curPage', Request::url());
//         Cookie::make('curPage', Request::url());
//     }
});

# CSRF保护过滤器，防止跨站点请求伪造攻击
Route::filter('csrf', function()
{
    if (Request::getMethod() !== 'GET' && Session::token() !== Input::get('_token'))
        throw new Illuminate\Session\TokenMismatchException;
});

# 必须是管理员
Route::filter('admin', function () {
    // 拦截非管理员用户，跳转回上一页面
    if (!Session::get('admin_user_id')) return Redirect::back();
});

# 必须是后台登录用户
Route::filter('admin-auth', function () {
    // 拦截未登录用户并记录当前 URL，跳转到登录页面
    if (!Session::get('admin_user_id')) return Redirect::guest(route('admin-signin'));

});

# 必须是登录用户
Route::filter('user-auth', function () {
    // 拦截未登录用户并记录当前 URL，跳转到登录页面
    if (!Auth::user()->check()) return Redirect::guest(route('signin'));
});

// # HTTP 基础身份验证过滤器 - 单次弹窗登录验证
// Route::filter('auth.basic', function () {
//     return Auth::basic();
// });

// # 必须是游客（较少应用）
Route::filter('guest', function () {
    // 拦截已登录用户
    if (Session::get('admin_user_id')) return Redirect::route('admin.frameset');
    else if (Session::get('user_id')) return Redirect::to('/');
});

// # 禁止对自己的账号进行危险操作
Route::filter('not.admin.self', function ($route) {
    // 拦截自身用户 ID
    if (Session::get('admin_user_id') == $route->parameter('id'))
        return Redirect::back();
});
// # 禁止对自己的账号进行危险操作
Route::filter('not.user.self', function ($route) {
    // 拦截自身用户 ID
    if (Session::get('user_id') == $route->parameter('id'))
        return Redirect::back();
});

Route::filter('enabled-actions', function($route) {
    $ca = Route::currentRouteAction();
    $roleIds = Session::get('CurUserRole');
    if (!$roleIds || $roleIds == '') {
        return App::make('AdminAuthorityController')->logout();
        // return Redirect::back()->with('error', '<strong>无法获取用户权限，请重新登录。</strong>');
    }
//    $roleIds = explode(',', $curUserRoles);
    $adminRoleId = Role::ADMIN;
    $data = App::make('BaseController')->getUserRights(2, true);
    $enabled = false;

    if (in_array($adminRoleId, $roleIds) || in_array($ca, $data)) $enabled = true;
    if (!$enabled) {
        return Redirect::back()->with('error', '<strong>您没有没有权限执行或访问该页面。</strong>');
    }
});

/*
|--------------------------------------------------------------------------
| [后置] 过滤器
|--------------------------------------------------------------------------
# Route::filter('afterFilter', function ($route, $request, $response) {});
|
*/



/*
|--------------------------------------------------------------------------
| 事件监控
|--------------------------------------------------------------------------
|
*/
# 用户登录事件
Event::listen('auth.login', function () {
    // 记录最后登录时间
    if (Session::get('admin_user_id')) {
        $user = Auth::admin()->get();
    }
    $user->signin_at = Carbon::now()->toDateTimeString();
    $user->save();
    // 后期可附加权限相关操作
    // ...
});
Event::listen('illuminate.query', function($query, $params, $time, $conn)
{
    // dd(array($query, $params, $time, $conn));
    // \Log::sql($query."\n");
    // \Log::sql(json_encode($params)."\n");
});
# 用户退出事件
// Event::listen('auth.logout', function ($user) {
//     //
// });