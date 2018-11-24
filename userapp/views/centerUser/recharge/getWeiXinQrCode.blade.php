@extends('l.home')

@section('title')
    支付宝充值 - 充值
    @parent
@stop


@section ('styles')
    @parent
    {{ style('proxy-global') }}
    {{ style('proxy') }}
    <style type="text/css">
        .main-content .content {
            padding-top: 10px;
        }
        .main {padding: 0;margin-top: 0}
        .layout-row {float: left;}
        .page-content .row {
            padding: 20px 0 10px 0;
            margin: 0;
        }
        .page-content-inner {
            box-shadow: 1px 1px 10px rgba(102, 102, 102, 0.1);
            border:0px solid #CCC;
            border-top: 0;
            background: #FFF;
        }
        .table-field td {
            padding: 5px 0;
        }
    </style>
@stop


@section ('main')
    <div class="nav-inner nav-bg-tab">
        <div class="title-normal">
            充值
        </div>
        @include ('centerUser.recharge_agent._bank_tab')
    </div>

    <div class="content page-content-inner recharge-netbank">
        <div class="prompt">
            请开启微信，扫描二维码成功后根据步骤提示完成支付。
        </div>
        <table width="100%" class="table-field">
            <tr>
                <td align="center"><img src="{{$aResponse['break_url']}}"/></td>
            </tr>
        </table>
    </div>
@stop

