<?php
$is_active = function ($name = '') use ($active) {
    if ($active === $name)
        return ' class="active"';
    else
        return '';
}
?>
<ul class="sidebar-nav" id="accordion">

    <li class="sidebar-collapse">
        <a href="javascript:void(0);" class="sidebar-collapse-icon"><span class="glyphicon glyphicon-align-justify"></span></a>
    </li>
    <li class="sidebar-list ">
        @foreach ($menus as $key => $menu)
        <a class="active" data-toggle="collapse"  data-toggle="collapse"  cdata-parent="#accordion" href="#collapseFor{{ $key }}">
        <i class="glyphicon glyphicon-screenshot"></i> {{ __($menu['title']) }} <i class="glyphicon glyphicon-chevron-down pull-right t5"></i>
        </a>

        <ul class="sub-menu collapse in" id="collapseFor{{ $key }}" >
            <?php if ( !isset($menu['children']) || !count($menu['children']) ) continue; ?>
            @foreach ($menu['children'] as $subMenu)

                <li>
                    <a {{ $is_active(explode('.', $subMenu['route_name'])[0]) }} href="{{ route($subMenu['route_name']) }}">{{ __( $subMenu['title']) }}</a>
                </li>
            @endforeach
        </ul>
        @endforeach
    </li>
</ul>

