@extends('l.sports')


@section ('container')
@include('jc.header')



<div class="layout-main">
    <div class="container">
        <div class="inner">


            <div class="line-list-top"></div>
            
            <div class="nav-cont">{{ $oJcLottery->name}}{{ $oMethodGroup->name }} 第{{ date('Ymd', strtotime($oBet->created_at)) }}期自购方案</div>
            

            <div class="panel-info">
                方案编号: {{ $oBet->serial_number }}
                &nbsp;&nbsp;
                发起时间: {{ $oBet->created_at }}
                &nbsp;&nbsp;
                方案状态: {{ $oBet->formatted_status }} 
                @if (in_array($oBet->status, [\JcModel\JcBet::STATUS_WON, \JcModel\JcBet::STATUS_PRIZE_SENT]))
                &nbsp;&nbsp;
                奖金：<span class="c-yellow">{{ number_format($oBet->prize, 4) }}</span> 元
                &nbsp;&nbsp;
                @if ($fSumCommission > 0)
                投注返点：<span class="c-yellow">{{ number_format($fSumCommission, 4) }}</span> 元
                @endif
                @endif
                <a href="{{ route('jc.bet_detail', $oBet->id) }}" style="float:right;color:#FFF;">查看方案明细</a>
            </div>
            

            <div class="bet-confirm">
                @include('jc.match', ['datas' => $datas, 'aWays' => $aWayList])
            </div>
        </div>
    </div>
</div>
@include('w.footer')
@stop




