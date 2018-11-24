@extends('l.home')

@section('title')
            追号记录 -- 详情
@parent
@stop

@section ('main')
<div class="nav-bg">
      <div class="title-normal">
        追号记录
      </div>
      <a id="J-button-goback" class="button-goback" href="#">返回</a>
    </div>

    <div class="content">
      <div class="area-search">

        <table width="100%" class="table-detail">
          <tr>
            <td width="184" align="right">游戏：</td>
              <td width="200">重庆时时彩</td>
              <td width="100" align="right">追号编号：</td>
              <td>D140523026VFBCCDBAEJ</td>
          </tr>
          <tr>
            <td align="right">玩法：</td>
              <td>后三直选</td>
              <td align="right">追号时间：</td>
              <td>2014-05-23 13:15:38</td>
          </tr>
          <tr>
            <td align="right">开始期号：</td>
              <td>140523026</td>
              <td align="right">追号期数：</td>
              <td>15期</td>
          </tr>
          <tr>
            <td align="right">完成期数：</td>
              <td>3期</td>
              <td align="right">取消期数：</td>
              <td>12期</td>
          </tr>
          <tr>
            <td align="right">追号金额：</td>
              <td>1,800.00</td>
              <td align="right">完成金额：</td>
              <td>800.00</td>
          </tr>
          <tr>
            <td align="right">取消金额：</td>
              <td>1,000.00</td>
              <td align="right">中奖后终止任务：</td>
              <td>是</td>
          </tr>
          <tr>
            <td align="right">追号状态：</td>
              <td>已完成</td>
              <td align="right">模式：</td>
              <td>元</td>
          </tr>
        </table>
        <div class="detail-row-cont">
          <div class="title">追号内容</div>
          <textarea disabled="disabled" class="textarea-lotterys-detail input"></textarea>
        </div>

      <table width="524" class="table-info table-toggle" style="margin-left:125px;">
        <thead>
          <tr>
            <th>奖期</th>
            <th>追号倍数</th>
            <th>追号内容</th>
            <th>追号状态</th>
            <th>注单详情</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>140523031</td>
            <td>1倍</td>
            <td>1278,346,0389</td>
            <td>已完成</td>
            <td><a href="#">终止本期追号</a>&nbsp;&nbsp;<a href="#">详情</a></td>
          </tr>
          <tr>
            <td>140523031</td>
            <td>1倍</td>
            <td>1278,346,0389</td>
            <td>已完成</td>
            <td><a href="#">终止本期追号</a>&nbsp;&nbsp;<a href="#">详情</a></td>
          </tr>
          <tr>
            <td>140523031</td>
            <td>1倍</td>
            <td>1278,346,0389</td>
            <td>已完成</td>
            <td><a href="#">终止本期追号</a>&nbsp;&nbsp;<a href="#">详情</a></td>
          </tr>

        </tbody>
      </table>



      <div class="detail-pages">

      @include('w.pages')
      </div>
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