@extends('l.home')

@section('title') 
    银行卡管理
    @parent
@stop


@section ('styles')
@parent
    <style type="text/css">
    .page-content .row {
        padding: 20px 0 10px 0;
        margin: 0;
    }
    .page-content-inner {
        box-shadow: 1px 1px 10px rgba(102, 102, 102, 0.1);
        border:0px solid #CCC;
    }
    </style>
@stop



@section ('container')

    @include('w.header')


    <div class="banner">
        <img src="/assets/images/proxy/banner.jpg" width="100%" />
    </div>





    <div class="page-content">
        <div class="g_main clearfix">
            @include('w.manage-menu')

            <div class="nav-inner clearfix">
                @include('w.uc-menu-user')
            </div>

            

            <div class="page-content-inner page-content-inner-bg">





                <table class="table" style="border:none;">
                    <tr>
                        <th>银行名称</th>
                        <th>卡号</th>
                        <th>绑定时间</th>
                        <th>银行卡状态</th>
                        <th>操作</th>
                    </tr>
                    @foreach ($datas as $data)
                    <tr>
                        <td>{{{ $data->bank }}}</td>
                        <td>{{{ $data->account_hidden }}}</td>
                        <td>{{{ $data->updated_at }}}</td>
                        <td>{{{ $data->{$aListColumnMaps['status']} }}}</td>
                        <td>&nbsp;</td>
                    </tr>
                    @endforeach
                </table>


                <div style="margin-top:20px;text-align:center;">
                        @if(isset($iBindedCardsNum) && (int)$iBindedCardsNum > 0)
                            @if((int)$iBindedCardsNum < $iLimitCardsNum)
                            <a href="{{ route('bank-cards.bind-card', 0) }}" class="btn">增加绑定</a>
                            @endif
                        @else
                            @if((int)$iBindedCardsNum < $iLimitCardsNum)
                            <a href="{{ route('bank-cards.bind-card', 1) }}" class="btn">增加绑定</a>
                            @endif
                        @endif
                        @if ($bLocked)
            <!--            <a href="{{ route('bank-cards.card-lock', 0) }}" class="btn">解锁银行卡</a>-->
                        @else
                        <a href="{{ route('bank-cards.card-lock', 1) }}" class="btn">锁定银行卡</a>
                        @endif
                </div>

                <br />


                <div class="row-tip" style="line-height:200%;padding:20px 0 20px 240px;">
                    1. 一个游戏账户最多绑定 {{{ $iLimitCardsNum }}} 张银行卡， 您目前绑定了{{{ $iBindedCardsNum }}}张卡，还可以绑定{{{ $iLimitCardsNum - $iBindedCardsNum }}}张。<br />
                    2. 当您的帐号金额超过1000元，系统将自动“锁定”您的提款银行卡.被锁定后不能新增银行卡，已添加的银行卡也将不能被修改.为了资金安全，您也可以选择手动锁定.<br />
                    3. 为了您的账户资金安全，银行卡“新增”和“修改”将在操作完成{{UserWithdrawal::WITHDRAWAL_TIME_LIMIT}}小时0分后，新卡才能发起“向平台提现”。
                </div>

            </div>
        </div>
    </div>



    @include('w.footer')
@stop



@section('end')
@parent
<script>
(function($){



})(jQuery);
</script>
@stop


