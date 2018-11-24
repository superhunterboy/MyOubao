@extends('l.home')

@section('title') 
    个人资料
    @parent
@stop


@section ('styles')
@parent
    {{ style('proxy-global') }}
    {{ style('proxy') }}
    <style type="text/css">
    .layout-row {float: left;}
    </style>
    <style type="text/css">
    .page-content .row {
        padding: 20px 0 10px 0;
        margin: 0;
    }
    .page-content-inner {
        box-shadow: 1px 1px 10px rgba(102, 102, 102, 0.1);
        border:0px solid #CCC;
        border-top: 0;
    }
    </style>
@stop



@section ('container')

    @include('w.header')


    <div class="banner">
        <img src="/assets/images/proxy/banner.jpg" width="100%" />
    </div>





    <div class="page-content page-content-password">
        <div class="g_main clearfix">
            @include('w.manage-menu')

            <div class="nav-inner clearfix">
                @include('w.uc-menu-user')
            </div>



            <div class="page-content-inner page-content-inner-bg">


                <div class="cont-personal clearfix">
                    <div class="layout-row">
                        <form action="{{ route('users.personal') }}" method="post" id="J-form-login">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                            <input type="hidden" name="_method" value="PUT" />
                            <table width="100%" class="table-field">
                                <tr>
                                    <td align="right"><span class="ico ico-personal"></span></td>
                                    <td><span class="title">个人资料&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
                                </tr>
                                <tr>
                                    <td align="right" style="width:150px;">用户名：</td>
                                    <td>
                                        {{ Session::get('username') }}
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right" valign="top">昵称：</td>
                                    <td>
                                        <input type="text" id="J-input-nickname" class="input w-4" name="nickname" value="" />
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right" valign="top"></td>
                                    <td>
                                        <input id="J-button-submit-nickname" class="btn" type="submit" value=" 提交修改 " />
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>
                    <div class="layout-row">

                        <table width="100%" class="table-field">
                            <tr>
                                <td align="right"><span class="ico ico-safe"></span></td>
                                <td><span class="title">账户安全&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
                            </tr>
                            <tr>
                                <td align="right" style="width:150px;"></td>
                                <td>
                                    <span class="text-lasttime">上一次登陆时间  {{Session::get('last_signin_at')}}</span>
                                </td>
                                <td>
                                </td>
                            </tr>
                        </table>

                    </div>
                </div>


                <div class="cont-personal clearfix">
                    <div class="layout-row layout-row-last">

                    <form action="{{ route('users.password-management', 0) }}" method="POST" id="J-form-login">
                        <input type="hidden" name="_method" value="PUT" />
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <table width="100%" class="table-field">
                        <tr>
                            <td align="right"><span class="ico ico-password"></span></td>
                            <td><span class="title">修改登录密码&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
                        </tr>
                        <tr>
                            <td align="right" style="width:150px;">输入旧登录密码：</td>
                            <td>
                                <input id="J-input-login-password-old" type="password" class="input w-4" name="old_password" placeholder="为了您的资金安全请使用虚拟键盘">
                                <span class="key-logo"></span>
                            </td>
                            <td>
                            </td>
                        </tr>
                        <tr>
                            <td align="right" valign="top">输入新登录密码：</td>
                            <td style="padding-bottom:0;">
                                <input id="J-input-login-password-new" type="password" class="input w-4" name="password" placeholder="为了您的资金安全请使用虚拟键盘">
                                <span class="key-logo"></span>
                                <div class="tip">
                                    6-16位字符，可使用字母或数字，不能和资金密码相同
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td align="right">确认新登录密码：</td>
                            <td>
                                <input id="J-input-login-password-new2" type="password" class="input w-4" name="password_confirmation" placeholder="为了您的资金安全请使用虚拟键盘">
                                <span class="key-logo"></span>
                            </td>
                            <td>
                            </td>
                        </tr>
                        <tr>
                            <td align="right" valign="top"></td>
                             <td>
                                <input class="btn" type="submit" value=" 提交修改 " />
                             </td>
                            <td>
                            </td>
                        </tr>
                    </table>
                    </form>

                    </div>

                    @if ($bFundPasswordSetted)
                    <form action="{{ route('users.password-management', 1) }}" method="post" id="J-form-money">
                        <input type="hidden" name="_method" value="PUT" />
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <div class="layout-row layout-row-last">

                        <table width="100%" class="table-field">
                            <tr>
                                <td align="right"><span class="ico ico-password-safe"></span></td>
                                <td><span class="title">修改资金密码&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
                            </tr>
                            <tr>
                                <td align="right" style="width:150px;">输入旧资金密码：</td>
                                <td>
                                    <input id="J-input-money-password-old" type="password" class="input w-4" name="old_fund_password" placeholder="为了您的资金安全请使用虚拟键盘">
                                    <span class="key-logo"></span>
                                </td>
                                <td>
                                </td>
                            </tr>
                            <tr>
                                <td align="right" valign="top">输入新资金密码：</td>
                                <td style="padding-bottom:0;">
                                    <input id="J-input-money-password-new" type="password" class="input w-4" name="fund_password" placeholder="为了您的资金安全请使用虚拟键盘">
                                    <span class="key-logo"></span>
                                    <div class="tip">
                                        6-16位字符，必须包含字母和数字，不允许连续三位相同，不能和登录密码相同
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td align="right">确认新资金密码：</td>
                                <td>
                                    <input id="J-input-money-password-new2" type="password" class="input w-4" name="fund_password_confirmation" placeholder="为了您的资金安全请使用虚拟键盘">
                                    <span class="key-logo"></span>
                                </td>
                                <td>
                                </td>
                            </tr>
                        <tr>
                            <td align="right" valign="top"></td>
                             <td>
                                <input class="btn" type="submit" value=" 提交修改 " />
                             </td>
                            <td>
                            </td>
                        </tr>
                        </table>
                    </div>
                    </form>
                    @else
                    <div class="row-tip" style="padding: 70px; margin: 93px 0 10px 10px;float: left;">为了保证您的资金安全，请立即设置您的资金密码 &nbsp;&nbsp;<a class="btn" href="{{ route('users.safe-reset-fund-password') }}">立即设置</a></span>
                    @endif
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

    $('#J-button-submit-nickname').click(function(){
        var v = $.trim($('#J-input-nickname').val());
        if(v.length < 2 || v.length > 16){
            alert('昵称必须由2至6个字符组成，请重新输入');
            $('#J-input-nickname').focus();
            return false;
        }
        return true;
    });

    //键盘单例
    var keyboard = new bomao.Keyboard({'isQueue':false});

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
            keyboard.show(-300,30,$(this));
        }
    });

})(jQuery);
</script>
@stop


