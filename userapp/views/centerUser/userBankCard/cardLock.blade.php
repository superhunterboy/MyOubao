@extends('l.home')

@section('title') 
    锁定银行卡
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


                <div class="row-tip">
                    为了账户的资金安全，建议锁定银行卡信息。<br>
                    锁定后不能增加新卡绑定，已绑定的银行信息不能进行修改和删除。
                </div>
                <form action="{{ route('bank-cards.card-lock') }}" method="post" id="J-form" autocomplete="off">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <table width="100%" class="table-field">
                        @foreach($aBindedCards as $key => $oCard)
                        <tr>
                            <td align="right">已绑卡{{ $key + 1 }}：</td>
                            <td>
                                <!-- <input type="hidden" name="id[]" value="{{-- $oCard->id --}}"> -->
                                {{ $oCard->bank . '--' . $oCard->account_hidden }}
                            </td>
                        </tr>
                        @endforeach
                        <tr>
                            <td align="right">资金密码：</td>
                            <td>
                                <input type="password" class="input w-4" id="J-input-password" name="fund_password" placeholder="为了您的资金安全请使用虚拟键盘"/>
                                <span class="key-logo"></span>
                            </td>
                        </tr>
                        <tr>
                            <td align="right"></td>
                            <td>
                                <input type="submit" value="{{ $bLocked ? '解除' : '提交' }}锁定" class="btn" id="J-submit">
                                <a href="{{ route('bank-cards.index') }}" class="btn">返 回</a>
                                <!-- <input type="button" value="返 回" class="btn btn-normal" id="J-button-back"> -->
                            </td>
                        </tr>
                    </table>
                </form>






            </div>
        </div>
    </div>



    @include('w.footer')
@stop



@section('end')
@parent
<script>
    (function($){
        $('#J-submit').click(function(){
            var password = $('#J-input-password'),v = $.trim(password.val());
            if(v == ''){
                alert('资金密码不能为空');
                password.focus();
                return false;
            }
            return true;
        });


        $('#J-button-back').click(function(){
            history.back(-1);
        });

        //键盘单例
        var keyboard = new bomao.Keyboard({'inputTag':$('.key-logo').prev() , 'isQueue':false});
        keyboard.show(30,-50,$('.key-logo'));
        $('.key-logo').addClass('key-active');

        $('.key-logo').click(function(){
            //获取将要输入的INPUT元素
            keyboard.inputTag = $(this).prev();
            $(this).prev().focus();
            //键盘显示
            if($(this).hasClass('key-active')){
                keyboard.hide();
                $(this).removeClass('key-active');
            }else{
                $('.key-logo').removeClass('key-active');
                $(this).addClass('key-active');
                keyboard.show(30,-50,$(this));
            }
        });

    })(jQuery);
</script>
@stop


