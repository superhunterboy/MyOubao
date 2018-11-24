<!DOCTYPE HTML>
<html lang="en-US">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge"  />
        <meta name="description" content="@yield('description')" />
        <meta name="keywords" content="@yield('keywords')" />
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
        <title>
            @section('title')
            -欧豹娱乐
            @show
        </title>
        @section ('styles')
            <style type="text/css">
                @font-face {
                font-family: 'Dayu';
                src: url('/assets/images/global/Dayu.eot');
                src: local('?'), url('/assets/images/global/Dayu.woff') format('/assets/images/global/woff'), url('/assets/images/global/Dayu.ttf') format('truetype'), url('/assets/images/global/Dayu.svg') format('svg');
                }
            </style>
            {{ style('global-v3')}}
            {{ style('font-awesome')}}
        @show
    </head>

    <body>
        <input type="hidden" name="_token" id="J-global-token-value" value="" />

        @yield('container')



        @section('scripts')
            {{ script('base-all') }}
        @show

        @include('w.notification')

        @section('end')

        @show

    </body>
</html>
