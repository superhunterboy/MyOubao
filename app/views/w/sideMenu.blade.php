<!DOCTYPE html>
<?php // pr($menus);?>
<html>
  <head>
    <title>
        @section('title')
        @show{{-- 页面标题 --}}
    </title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('description')">{{-- 页面描述 --}}
    <meta name="keywords" content="@yield('keywords')" />    {{-- 页面关键词 --}}
    <!--[if lt IE 9]>
        <script src="http://cdn.bootcss.com/html5shiv/3.7.0/html5shiv.min.js"></script>
        <script src="http://cdn.bootcss.com/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <script>
        (function(H){H.className=H.className.replace(/\bno-js\b/,'js')})(document.documentElement)
    </script>
    @section ('styles')
      {{ style('bootstrap-3.0.3')}}
      {{ style('main' )}}
      {{ style('ui' )}}
    @show


  </head>
  <body style="background:#EAEAEA;">
<ul class="sidebar-nav" id="accordion">

    <li class="sidebar-collapse" style="display:none;">
        <a href="javascript:void(0);" class="sidebar-collapse-icon"><span class="glyphicon glyphicon-align-justify"></span></a>
    </li>
    @foreach ($menus as $key => $menu)
    <?php if ( !isset($menu['children']) || !count($menu['children']) ) continue; ?>
    <li class="sidebar-list ">

        <a data-toggle="collapse"  data-toggle="collapse"  cdata-parent="#accordion" href="#collapseFor{{ $key }}">
        <i class="glyphicon glyphicon-chevron-right"></i>
        <i class="glyphicon glyphicon-chevron-down"></i>
        &nbsp;
         {{ Str::title(__('_function.' . $menu['title'])) }}
        </a>

        <ul class="sub-menu collapse " id="collapseFor{{ $key }}" >
            @foreach ($menu['children'] as $subMenu)
            <?php
                if ($subMenu['route_name']){
                    $sUrl = route($subMenu['route_name']);
                    !$subMenu['params'] or $sUrl .= '?' . $subMenu['params'];
                }
                else{
                    $sUrl = 'javascript:void(0);';
                }
                $sTarget = $subMenu['new_window'] ? '_blank' : 'main';
            ?>
                <li>
                    <a target="{{ $sTarget }}"  href="{{ $sUrl }}">{{ Str::title(__( '_function.' . $subMenu['title'])) }}</a>
                </li>
            @endforeach
        </ul>

    </li>
    @endforeach
</ul>



    @section('javascripts')
      {{ script('jquery-1.10.2') }}
      {{ script('bootstrap-3.0.3') }}
      {{ script('bootstrap-ie')}}

    @show

    @section('js-code')
      {{ script('base')}}
    @show

    @section('end')
    @show
  </body>
</html>