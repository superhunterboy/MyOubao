<!DOCTYPE HTML>
<html lang="zh-CN">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="description" content="@yield('description')" />
        <meta name="keywords" content="@yield('keywords')" />
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
        <title>
            @section('title')
            -博猫彩票
            @show
        </title>

        @section ('styles')
        {{ style('bootstrap')}}
        {{ style('indexClient-hf')}}
        {{ style('indexClient-lr')}}
        {{ style('global-v4')}}
        {{ style('outer')}}
        <!--[if lt IE 9]>
          <script src="//cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
        @show
    </head>
    <body>
        @yield('container')



        @section('scripts')
            {{ script('base-all') }}
            {{ script('bootstrap') }}
        @show
        @include('w.notification')
        @section('end')
        @show

    </body>
</html>