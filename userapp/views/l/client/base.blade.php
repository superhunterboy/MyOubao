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
            -博猫彩票
            @show
        </title>
        @section ('styles')
            <style type="text/css">
                @font-face {
                font-family: 'Bomao';
                src: url('/assets/images/global/Bomao.eot');
                src: local('?'), url('/assets/images/global/Bomao.woff') format('/assets/images/global/woff'), url('/assets/images/global/Bomao.ttf') format('truetype'), url('/assets/images/global/Bomao.svg') format('svg');
                }
                @font-face{
                    font-family: dyAvenir;
                    src:url('/assets/images/global/dyAvenir.ttf');
                }

   /*             @font-face{
                    font-family:dyFzzhs;
                    src:url('/assets/images/global/dyFzzhs.ttf');
                }*/
                @font-face{
                    font-family:dyBebas;
                    src:url('/assets/images/global/dyBebas.ttf');
                }

                @font-face{
                    font-family:FZLanTingHei-R-GBK;
                    src:url('/assets/images/global/FZLanTingHei-R-GBK.ttf');
                }

                @font-face {
                    font-family: 'open24';
                    src: url( /assets/images/game/pk10/open24.eot ); /* IE */
                    src: url( /assets/images/game/pk10/open24.ttf ) format("truetype");  /* 非IE */
                }

            </style>

            {{ style('indexClient-hf')}}
            {{ style('indexClient-lr')}}
            {{ style('global-v4')}}
            {{ style('font-awesome')}}
            @show
            {{ script('jquery-1.9.1') }}

    </head>

    @section('body')
    <body>
        <input type="hidden" name="_token" id="J-global-token-value" value="" />

        @section('start')

        @show

        @yield('container')

        

        


        @section('scripts')
            {{ script('base-all') }}
            {{ script('jquery.easing.1.3') }}
            {{ script('jquery.flexslider') }}
            {{ script('jquery.lavalamp') }}


        @show

        @include('w.notification')

        @section('end')

        @show

    </body>
    @show

</html>
