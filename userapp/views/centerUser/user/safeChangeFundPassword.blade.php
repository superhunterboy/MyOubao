@extends('l.home')

@section('title') 
    设置资金密码
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
        <div class="g_33 clearfix">
            @include('w.manage-menu')

            <div class="nav-inner clearfix">
                <ul class="list clearfix">
                    <li class="active"><span class="top-bg"></span><a href="{{ route('users.password-management')}}">密码管理</a></li>
                    <li><a href="{{ route('security-questions.index')}}">安全口令</a></li>
                    <li><a href="{{ route('bank-cards.index') }}">银行卡管理</a></li>
                    <li><a href="{{ route('user-user-prize-sets.game-prize-set')}}">我的奖金</a></li>
                </ul>
            </div>



            <div class="page-content-inner">

                <div class="row-tip" style="text-align:center;">
                    为了你的账户安全，请先设置资金密码，该密码用于验证您的资金操作。
                </div>

                <form action="{{ route('users.safe-reset-fund-password') }}" method="post" id="J-form">
                    <input type="hidden" name="_method" value="PUT" />
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <table width="100%" class="table-field">
                        <tr>
                            <td width="450" align="right">设置资金密码：</td>
                            <td width="240">
                                <input type="password" class="input w-4" id="J-input-passowrd" name="fund_password" placeholder="为了您的资金安全请使用虚拟键盘" />
                                <span class="key-logo"></span>
                            </td>
                            <td>
                                <span class="ui-text-prompt-multiline w-6">6-16位字符，必须包含字母和数字，不允许连续三位相同，不能和登录密码相同</span>
                                <div class="col-sm-4">
                                    {{ $errors->first('fund_password', '<label class="text-danger control-label">:message</label>') }}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td align="right">确认资金密码：</td>
                            <td>
                                <input type="password" class="input w-4"id="J-input-passowrd2" name="fund_password_confirmation" placeholder="为了您的资金安全请使用虚拟键盘"/>
                                <span class="key-logo"></span>
                            </td>
                            <td>
                                <span class="ui-text-prompt-multiline w-6">再次输入资金密码</span>
                                <div class="col-sm-4">
                                    {{ $errors->first('fund_password_confirmation', '<label class="text-danger control-label">:message</label>') }}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td align="right">&nbsp;</td>
                            <td><input class="btn" type="submit" value=" 提 交 " id="J-submit" /></td>
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
    var ipt1 = $('#J-input-passowrd'),
    ipt2 = $('#J-input-passowrd2');

    $('#J-submit').click(function(){
        var v1 = $.trim(ipt1.val()),
          v2 = $.trim(ipt2.val());
        if(v1 == ''){
          alert('资金密码不能为空');
          ipt1.focus();
          return false;
        }
        if(v2 == ''){
          alert('确认资金密码不能为空');
          ipt2.focus();
          return false;
        }
        if(v1 != v2){
          alert('两次输入的资金密码不一致');
          ipt2.focus();
          return false;
        }
        if(!(/^(?=.*\d+)(?=.*[a-zA-Z]+)(?!.*?([a-zA-Z0-9]{1})\1\1).{6,16}$/).test(passwordNewV)){
            alert('新资金密码格式不符合要求');
            ipt2.focus();
            return false;
        }
        return true;
    });

    $('#J-button-goback').click(function(e){
        history.back(-1);
        e.preventDefault();
    });

    //键盘单例
    var keyboard = new bomao.Keyboard({'inputTag':$('.key-logo').eq(0).prev() , 'isQueue':false});
    keyboard.show(-220,30,$('.key-logo').eq(0));
    $('.key-logo').eq(0).addClass('key-active');

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
            keyboard.show(-220,30,$(this));
        }
    });

})(jQuery);
</script>

@stop


