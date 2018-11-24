@extends('l.home')

@section('title')
            注单详情
@parent
@stop


@section ('main')



<div class="nav-inner clearfix">
    <ul class="list">
        <li><a href="{{ route('projects.index') }}">返回列表</a></li>
        <li class="active"><span class="top-bg"></span><a href="">游戏详情</a></li>
    </ul>
    
</div>


    <div class="content">
      <div class="area-search" style="background:#FFF;">


        <div class="openball-result">

                @foreach ($data->splitted_winning_number as $number)
                  <span class="item item-{{$sSeriesName}} item-{{$sSeriesName}}-{{ intval($number) }}">{{ $number }}</span>
                @endforeach
        </div>

        <table width="100%" class="table-detail">
          <tr>
            <td width="400" align="right">游戏：</td>
              <td><span class="value">{{ $aLotteries[$data->lottery_id] }}</span></td>
              <td align="right">注单编号：</td>
              <td><span class="value">{{ $data->serial_number }}</span></td>
          </tr>
          <tr>
            <td align="right">玩法：</td>
              <td><span class="value"> {{ $data->title }}</span></td>
              <td align="right">投注时间：</td>
              <td><span class="value">{{ $data->created_at }}</span></td>
          </tr>
          <tr>
            <td align="right">期号：</td>
              <td><span class="value">{{ $data->issue }}</span></td>
              <td align="right">倍数：</td>
              <td><span class="value">{{ $data->multiple}}倍</span></td>
          </tr>

          <tr>
            <td align="right">模式：</td>
              <td><span class="value">{{ $aCoefficients[$data->coefficient] }}</span></td>
              <td align="right">投注金额：</td>
              <td><span class="value">{{ $data->amount_formatted }}</span></td>
          </tr>

          <tr>
            <td align="right">状态：</td>
            <td><span class="value">{{ $data->formatted_status }}</span></td>
            <td align="right">奖金：</td>
            <td>
                <span class="value"> {{ $data->prize_formatted }}</span> 
            </td>
          </tr>
          <tr>
            <td align="right">注单ID：</td>
            <td><span class="value">{{ $data->id }}</span></td>
            <td align="right">投注返点：</td>
            <td>
                <span class="value">@if($data->status_commission == Project::COMMISSION_STATUS_SENT){{ $data->getCommissionAmount() }} @endif</span>
            </td>
          </tr>


        </table>


        <div class="detail-row-cont">
            <div class="title">投注内容
                  @if($data->position)
                    位置: {{$data->position_string}}
                  @endif
                @if ($data->trace_id)
                (<a href="{{ route('traces.view', $data->trace_id) }}">相关追号记录</a>)
                @endif
            </div>
            <textarea disabled="disabled" class="textarea-lotterys-detail input">{{ $data->formatted_display_bet_number }}</textarea>
        </div>




        <p class="row align-center">
          <a class="btn goback" href="{{ route('projects.index') }}">返回</a>

          @if ($data->status == Project::STATUS_NORMAL)
          <a class="btn" id="cancelProject" href="javascript:void(0);">撤单</a>

          <a class="btn" id="printProject" href="javascript:void(0);">打印预览</a>
          @endif
        </p>



      </div>

      <div id="print-info">
        <style type="text/css">

          #bet-content-info{
              margin:50px auto 40px auto;
              border:1px solid #C6C6CC;
              max-width: 600px;
              min-width: 200px;
              font-size: 26px;
          }

          #bet-content-info .bet-content-list{
              list-style: none;
              padding: 0px;
              width: 90%;
              padding: 0;
              margin-left: 5%;
          }

          #bet-content-info .bet-content-list li .bet-info-type{
              display: inline-block;
              padding: 5px 0;
              width: 150px;
          }

          #bet-content-info .bet-content-list .code{
              display: inline-block;
              width: 100%;
              padding-top: 30px;
              padding-bottom: 5px;
              text-align: center;
              font-size: 26px;
          }

          #bet-content-info .bet-content-list .code-area{
              border: 1px solid #C6C6CC;
              height: 50px;
              line-height: 50px;
              margin-bottom: 20px;
              background-color: #FFFFBD;
              font-size: 26px;
              text-align: center;
          }

          #bet-content-info .bet-num-detail{
              word-wrap:break-word;
          }

          #bet-content-info .bet-detail{
              border:1px solid #C6C6CC;
              width: 100%;
              height: auto;
              word-wrap:break-word;
          }

          .pribt-btn{
              width: 80px;
              margin:10px auto;          
              height: 28px;
              line-height: 28px;
              padding: 0 29px;
              text-align: center;
              font-size: 16px;
              color: #FFF;
              cursor: pointer;
              font-family: microsoft yahei;
              background-image: -webkit-linear-gradient(top,#31CEAC,#54C28E);
              background-image: -moz-linear-gradient(top,#31CEAC,#54C28E);
              background-image: -o-linear-gradient(top,#31CEAC,#54C28E);
              background-image: linear-gradient(top,#31CEAC,#54C28E);
              background-color: #31CEAC;
              box-shadow: 0 1px 2px rgba(0,0,0,.2);
          }

          .pribt-btn:hover{
            color: #484848;
          }
        </style>

        <style type="text/css" media=print>
          .noprint {
              display: none;
          }
        </style>

        <div id="bet-content-info">
          <title>订单信息预览</title>
          <ul class="bet-content-list">
            <li>
              <span class="bet-info-type">游戏：</span>
              <span>{{ $aLotteries[$data->lottery_id] }}</span>
            </li>
            <li>
              <span class="bet-info-type">玩法：</span>
              <span>{{ $data->title }}</span>
            </li>
            <li>
              <span class="bet-info-type">期号：</span>
              <span>{{ $data->issue }}</span>
            </li>
            <li>
              <span class="bet-info-type">倍数：</span>
              <span>{{ $data->multiple}}倍</span>
            </li>
            <li>
              <span class="bet-info-type">模式：</span>
              <span>{{ $aCoefficients[$data->coefficient] }}</span>
            </li>
            <li>
              <span class="bet-info-type">投注金额：</span>
              <span>{{ $data->amount_formatted }}</span>
            </li>
            <li>
              <span class="bet-info-type">投注编号：</span>
              <span class="bet-num-detail">{{ $data->serial_number }}</span>
            </li>
            <li>
              <span class="bet-info-type">投注内容：</span></br>
              <div class="bet-detail">{{ $data->formatted_display_bet_number }}</div>
            </li>
            <li>
              <span class="code">兑奖验证码：</span></br>
              <div class="code-area">{{ $data->id }}</div>
            </li>
          </ul>

        </div>

        <div class="pribt-btn noprint" onclick="window.print();">打印</div>
      </div>
      

    </div>

    
@stop

@section('end')
@parent
<script>
(function($){
    // $('#cancelProject').click(function(event) {
    //     if (confirm('')) {
    //         location.href = "{{ URL::route('projects.drop',['id' => $data->id]) }}";
    //     }
    // });
    $('#cancelProject').click(function(event) {
        var popWindowNew = bomao.Message.getInstance();
        var data = {
            title          : '确认',
            content        : "<i class=\"ico-waring\"></i><p class=\"pop-text\">您确认撤单么？</p>",
            isShowMask     : true,
            cancelIsShow   : true,
            confirmIsShow  : true,
            cancelButtonText: '取消',
            confirmButtonText: '确认',
            confirmFun     : function() {
                location.href = "{{ route('projects.drop',['id' => $data->id]) }}";
            },
            cancelFun      : function() {
                this.hide();
            }
        };
        popWindowNew.show(data);
    });
    // debugger;

//   $('#J-button-goback , .goback').click(function(e){
//     history.back(-1);
//     e.preventDefault();
//   });

    $('#printProject').click(function(event) {
      myWindow=window.open('','','width=500,height=800,left=300,top=25');
      myWindow.document.write($('#print-info').html());
    });

    $('#print-info').hide();
})(jQuery);

</script>
@stop