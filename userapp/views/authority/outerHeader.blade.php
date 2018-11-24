@if(Session::get('is_client'))
    @include('w.client.public-header')
@else
<header class="navbar">
  <div class="container">
	<div class="navbar-header">
      <a href="{{ route('home') }}" class="navbar-brand">欧豹娱乐</a>
    </div>


    <nav class="collapse navbar-collapse">
    

    
      <ul class="nav navbar-nav navbar-right">
        <li>
        	<a class="btn btn-quick" href="/pc-client/index.html" target="_blank">PC客户端下载</a>
				<div class="quick-code base-jmp animation" style="margin-left: -64px; display: none;">
					<div class="jmp-hd"><b class="tri-out"></b></div>
					<div class="jmp-bd">
						<p>快速访问，强化安全</p>
					</div>
				</div>
        </li>
        <li>
        	<a class="btn btn-phone" href="/mobile" target="_blank">手机客户端</a>
				<div class="phone-code base-jmp animation">
					<div class="jmp-hd"><b class="tri-out"></b></div>
					<div class="jmp-bd">
						<!-- <img src="/assets/images/reg/mobile-phone-code.png" alt="扫一扫，手机玩博猫" /> -->
						<p>随时随地，自由自在</p>
					</div>
				</div>
        </li>
        <li>
        	<a class="btn btn-wechat" href="#" target="_blank">博猫微信</a>
				<div class="wechat-code base-jmp animation" style="display: none; margin-left: -111.5px;">
					<div class="jmp-hd"><b class="tri-out"></b></div>
					<div class="jmp-bd">
						<p>官方微信：bomaogame</p>
						<img src="/assets/images/reg/phone-code.png" alt="扫一扫，手机玩博猫">
						<p>微信扫一扫，精彩全知道</p>
					</div>
				</div>
        </li>
      </ul>
    
    </nav>

  </div>
</header>



@section('end')
@parent
<script type="text/javascript">
    $('.navbar-right>li').hover(function(){
        var curLeft = - $(this).find('.base-jmp').outerWidth()/2
        $(this).find('.base-jmp').show().css("margin-left",curLeft).addClass("fadeInUp");
    }, function(){
        $(this).find('.base-jmp').hide().removeClass("fadeInUp");
    });
</script>
@stop






@endif


