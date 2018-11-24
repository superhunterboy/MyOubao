@extends('l.base-v4')

@section ('styles')
@parent
{{ style('chart') }}

{{script('base-all')}}
{{script('IE-excanvas')}}
{{ script('bomao.GameChart.Case') }}
{{ script('bomao.GameChart') }}

@stop

@section('body')

<body  class="table-trend">
    @include('w.header')
    @yield('container')
    @include('w.footer')
    @include('w.notification')

    @section('end')

    @show
</body>

@stop
<script type="text/javascript">
function openKF() {
	var url = '{{SysConfig::readValue("KFURL")}}';  //转向网页的地址;
    var name = '';                            //网页名称，可为空;
    var iWidth = 750;                          //弹出窗口的宽度;
    var iHeight = 500;                         //弹出窗口的高度;
    //获得窗口的垂直位置
    var iTop = (window.screen.availHeight - 30 - iHeight) / 2;
    //获得窗口的水平位置
    var iLeft = (window.screen.availWidth - 10 - iWidth) / 2;
    window.open(url, name, 'height=' + iHeight + ',,innerHeight=' + iHeight + ',width=' + iWidth + ',innerWidth=' + iWidth + ',top=' + iTop + ',left=' + iLeft + ',status=no,toolbar=no,menubar=no,location=no,resizable=no,scrollbars=0,titlebar=no');
}
</script>
