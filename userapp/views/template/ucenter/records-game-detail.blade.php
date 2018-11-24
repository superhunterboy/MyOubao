@extends('l.home')

@section('title')
            注单详情
@parent
@stop


@section ('main')

        <div class="nav-bg">
      <div class="title-normal">
        游戏记录
      </div>
      <a id="J-button-goback" class="button-goback" href="#">返回</a>
    </div>

    <div class="content">
      <div class="area-search">

        <table width="100%" class="table-detail">
          <tr class="noborder">
            <td colspan="4" align="center">
            <div class="title">
              开奖号码
            </div>
            </td>
          </tr>
          <tr class="noborder">
            <td colspan="4" align="center">

                <span class="ball">1</span>
                <span class="ball">2</span>
                <span class="ball">3</span>
                <span class="ball">4</span>
                <span class="ball">5</span>

          </td>
          </tr>
          <tr>
            <td width="160" align="right">游戏：</td>
              <td width="240"><span class="value">重庆时时彩</span></td>
              <td width="65" align="right">注单编号：</td>
              <td><span class="value">D140523026VFBCCDBAEJ</span></td>
          </tr>
          <tr>
            <td align="right">玩法：</td>
              <td><span class="value">后三直选</span></td>
              <td align="right">投注时间：</td>
              <td><span class="value">2014-05-23 13:15:38</span></td>
          </tr>
          <tr>
            <td align="right">期号：</td>
              <td><span class="value">140523026</span></td>
              <td align="right">倍数：</td>
              <td><span class="value">2倍</span></td>
          </tr>
          <tr>
            <td align="right">模式：</td>
              <td><span class="value">3期</span></td>
              <td align="right">投注金额：</td>
              <td><span class="value">800.00</span></td>
          </tr>
          <tr class="noborder">
            <td align="right">状态：</td>
              <td><span class="value">已完成</span></td>
              <td align="right">奖金：</td>
              <td><span class="value">800.00</span></td>
          </tr>
        </table>


        <div class="detail-row-cont">
          <div class="title">投注内容 (<a href="#">相关追号记录</a>)</div>
          <textarea disabled="disabled" class="textarea-lotterys-detail input"></textarea>
        </div>




        <p class="row align-center">
          <a class="btn" href="#">返回</a>

          <a class="btn" href="#">撤单</a>
        </p>



      </div>

    </div>
@stop

@section('end')
@parent
<script>
(function($){
  $('#J-button-goback').click(function(e){
    history.back(-1);
    e.preventDefault();
  });
})(jQuery);
</script>
@stop