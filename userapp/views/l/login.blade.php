@if(Session::get('is_client'))
    @include('l.client.login')
@else
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
            {{ style('global-v4')}}
        @show
    </head>

    <body>


        @yield('container')



        @section('scripts')
            {{ script('base-all') }}
        @show

        @include('w.notification')

        @section('end')

        @show




    </body>
</html>
@endif