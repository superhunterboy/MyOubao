@extends('l.home')

@section('title') 
    绑定结果
    @parent
@stop


@section ('styles')
@parent
    {{ style('proxy-global') }}
    {{ style('proxy') }}
    <style type="text/css">
    .page-content .row {
        padding: 20px 0 10px 0;
        margin: 0;
    }
    .page-content-inner {
        box-shadow: 1px 1px 10px rgba(102, 102, 102, 0.1);
        border:0px solid #E6E6E6;
        border-top: 0;
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


                <div class="step">
                    <table class="step-table">
                        <tbody>
                        @if (isset($bIsFirst) && $bIsFirst)
                            <tr>
                                <td class="clicked"><div class="con"><i>1</i>输入银行卡信息</div></td>
                                <td class="clicked"><div class="tri"><div class="con"><i>2</i>确认银行卡信息</div></div></td>
                                <td class="current"><div class="tri"><div class="con"><i>3</i>绑定结果</div></div></td>
                            </tr>
                        @else
                            <tr>
                                <td class="clicked"><div class="con"><i>1</i>验证老银行卡</div></td>
                                <td class="clicked"><div class="tri"><div class="con"><i>2</i>输入银行卡信息</div></div></td>
                                <td class="clicked"><div class="tri"><div class="con"><i>3</i>确认银行卡信息</div></div></td>
                                <td class="current"><div class="tri"><div class="con"><i>4</i>绑定结果</div></div></td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>

                @if ($bSucceed)
                <div class="row-tip" style="text-align:center;padding:50px 0">
                    <i></i>
                    <div class="txt">
                        <h4>恭喜你，银行卡绑定成功。</h4>
                        <p>新的银行卡将在2小时0分后可以发起“平台提现”</p>
                        <div>现在您可以： 
                            @if(Session::get('is_player'))
                            <a href="{{ route('user-recharges.netbank') }}" class="btn btn-small">充值</a>
                            @endif
                            &nbsp;&nbsp;<a href="{{ route('bank-cards.index') }}" class="btn btn-small">银行卡管理</a></div>
                    </div>
                </div>
                @else

                <div class="row-tip" style="text-align:center;padding:50px 0;">
                    <i></i>
                    <div class="txt">
                        <h4>银行卡绑定失败。</h4>
                        <div>现在您可以：  <a href="{{ route('bank-cards.bind-card', 0) }}" class="btn btn-small">重新绑定</a></div>
                    </div>
                </div>
                @endif







            </div>
        </div>
    </div>



    @include('w.footer')
@stop



@section('end')
@parent
<script>
    (function($){

        $('#J-button-back').click(function(){
            history.back(-1);
        });

    })(jQuery);
</script>
@stop


