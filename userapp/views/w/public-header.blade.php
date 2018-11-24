@if(Session::get('is_client'))
    @include('w.client.public-header')
@else
<style type="text/css">
			.global-top .chat a {
			    height: 40px;
			    line-height: 40px;
			    width: 85px;
			    text-align: right;
			    color: #000;
			}
        </style>

<div class="global-top">
    <div class="container clearfix">
        <a title="博狼娱乐首页" class="logo" href="{{ route('home') }}">博狼娱乐</a>
        <div class="chat" id="J-header-service">
            <a href="javascript:void(0);" onclick="openKF()" title="有疑问？请咨询在线客服">在线客服</a>
        </div>
    </div>

</div>
@endif



