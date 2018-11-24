{{-- 管理中心 侧边菜单栏与菜单层级显示 --}}
<?php $aUserCenterNav = UserCenterMenu::getNav(); ?>
<?php $aUserCenterMenu = UserCenterMenu::getMenu(); ?>
<?php $i=1; $total = count($aUserCenterNav); ?>
<div class="center-top-menu">
	<span class="menu-logo"></span>
                    @if(isset($aUserCenterNav) && $aUserCenterNav)
                        @foreach($aUserCenterNav as $k=>$m)
                            <span class="menu-content">{{$m}}</span>
                            @if(($k+1) != $total)
                                <span class="menu-sign">&gt;</span>
                            @endif
                        @endforeach
                    @endif

</div>

<div class="center-left-menu">
        @foreach($aUserCenterMenu as $k=> $aMenu)
	<div class="logo-box">
		<span class="logo-box-side"></span>
		<span class="logo-img logo-img-{{$i}}"></span>
		<div class="second-menu">
			<div class="title">{{$aMenu['title']}}</div>
                        
			<ul>
                @if( isset($aMenu['children']) && $aMenu['children'])
                @foreach($aMenu['children'] as $j=>$c)
                        @if('站内信' == $c['title'])
                                <li><a href="{{$c['url']}}"><span class="left-menu-item">&gt;&nbsp;&nbsp;</span>{{$c['title']}}<span class="letter-num">{{$unreadMessagesNum}}</span></a></li>
                        @elseif('高点配额' == $c['title'])
                            @if(Session::get('show_overlimit'))
                                <li><a href="{{$c['url']}}"><span class="left-menu-item">&gt;&nbsp;&nbsp;</span>{{$c['title']}}</a></li>
                             @endif
                         @elseif('分红报表' == $c['title'])
                            @if(Session::get('is_top_agent') )
                                <li><a href="{{$c['url']}}"><span class="left-menu-item">&gt;&nbsp;&nbsp;</span>{{$c['title']}}</a></li>
                            @endif
                         @else    
                                 <li><a href="{{$c['url']}}"><span class="left-menu-item">&gt;&nbsp;&nbsp;</span>{{$c['title']}}</a></li>
                        @endif
                @endforeach
                @endif  
            </ul>
		</div>
	</div>
                <?php $i++; ?>
                @endforeach
	
</div>

