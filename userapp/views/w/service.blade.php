

{{-- 在线客服 --}}
{{-- 
@section('end')
@parent
<script type="text/javascript">
@if(Session::get('user_id'))
//客服代码--
//姓名|性别|固定电话|手机|邮箱|地址|公司名|MSN|QQ|会员ID|会员等级 |（此处按照上面约定字段直接传送；如未登陆，传空）会员等级（1:VIP会员 0:普通会员）
var hjUserData="{{urlencode(Session::get('username'))}}|||||{{get_client_ip()}}||||{{Session::get('id')}}|0|";
@endif
(function($){


})(jQuery);

</script>
@stop
 --}}


