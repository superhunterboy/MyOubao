@extends('l.home')

@section('title') 
    删除银行卡
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

                <form action="{{ route('bank-cards.destroy', $iCardId) }}" method="post" id="J-form">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <input type="hidden" name="method" value="DELETE" />
                <table width="100%" class="table-field">
                    <tr>
                        <td align="right" width="460">卡号：</td>
                        <td>
                            <input type="hidden" name="id" value="{{ $iCardId }}">
                            {{ $sAccountHidden }}
                        </td>
                    </tr>
                    <tr>
                        <td align="right">开户人姓名：</td>
                        <td>
                            <input type="text" class="input w-4" id="J-input-name" name="account_name">
                            <span class="ui-text-prompt">请输入旧的银行卡开户人姓名</span>
                        </td>
                    </tr>
                    <tr>
                        <td align="right">银行账号：</td>
                        <td>
                            <input type="text" class="input w-4" id="J-input-card-number" name="account">
                            <span class="ui-text-prompt">请输入旧的银行卡卡号</span>
                        </td>
                    </tr>
                    <tr>
                        <td align="right">资金密码：</td>
                        <td>
                            <input type="password" class="input w-4" id="J-input-password" name="fund_password">
                            <span class="ui-text-prompt">请输入您的资金密码</span>
                        </td>
                    </tr>
                    <tr>
                        <td align="right"></td>
                        <td>
                            <input type="submit" value="删除" class="btn" id="J-submit">
                            <!-- <a href="#"  value="删 除" class="btn" id="J-submit">删除</a> -->
                            <a href="{{ route('bank-cards.index') }}"  value="取 消" class="btn btn-normal">取消</a>
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


})(jQuery);
</script>
@stop


