@extends('l.home')

@section('title')
    绑定结果 -- 增加绑定
@parent
@stop


@section('main')
<div class="nav-bg">
            <div class="title-normal">
                银行卡绑定
            </div>
        </div>

        <div class="content">


            <div class="step">
                <table class="step-table">
                    <tbody>
                        <tr>
                            <td class="current"><div class="con"><i>1</i>输入银行卡信息</div></td>
                            <td class="current"><div class="tri"><div class="con"><i>2</i>确认银行卡信息</div></div></td>
                            <td class="current"><div class="tri"><div class="con"><i>3</i>绑定结果</div></div></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="alert alert-success">
                <i></i>
                <div class="txt">
                    <h4>恭喜你，银行卡绑定成功。</h4>
                    <p>新的银行卡将在2小时0分后可以发起“平台提现”</p>
                    <div>现在您可以：  <a href="#" class="btn btn-small">充值</a>&nbsp;&nbsp;<a href="#" class="btn btn-small">银行卡管理</a></div>
                </div>
            </div>


            <div class="alert alert-error">
                <i></i>
                <div class="txt">
                    <h4>银行卡绑定失败。</h4>
                    <div>现在您可以：  <a href="#" class="btn btn-small">重新绑定</a></div>
                </div>
            </div>



        </div>
@stop