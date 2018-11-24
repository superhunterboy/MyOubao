@section('title')
    注册跳转
@parent
@stop


@section ('styles')
@parent
    {{ style('reg-v2') }}
{{ style('indexClient-lr')}}
@stop




@section('container')


@include('w.client.public-header')

<div class="reg">
    <div class="b">
        <i></i>
        <div class="text">
            <h4>恭喜，注册成功！</h4>
            <h5>{{Session::get("username")}},恭喜您成为博猫尊贵会员</h5>
            <h5><span id="trans-time">3</span>秒后为您跳至平台首页</h5>
        </div>
        <input type="button" id="btn-trans" value="点击进入"/>
    </div>
</div>

@include('w.client.footer')
@stop





@section('end')
@parent
    <script type="text/javascript">
    var dsq,
            time=3;

    dsq=setInterval(function () {
        time --;
        document.getElementById("trans-time").innerHTML = time;
        if(time<=0){
            clearInterval(dsq);
            location.href = "/";
        }

    },1000);

    $("#btn-trans").click(function(){
        location.href = "/";
    })
    </script>
@stop

































